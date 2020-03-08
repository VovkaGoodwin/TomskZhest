<?php
/**
 * Created by PhpStorm.
 * User: M.Game
 * Date: 13.10.2019
 * Time: 19:40
 */

namespace servicetech;

/**
 * Class SNMP
 *
 * For using some SNMP methods
 * If method read some information about ports, should statrt from $this->testPort($port)
 *
 * @package servicetech
 */
class SNMP
{

  private $model;
  private $ip;
  private $portsCount;
  private $connectStatus = TRUE;

  private $statePortOID;
  private $clearCrcOID;

  private $debug = FALSE;

  public function __construct($ip){
    $this->ip = $ip;
    $this->readModel();
    if ($this->connectStatus) {
      $this->countOfPorts();
      $this->startTestingAllPorts();
      $this->setOids();
    }
  }

  public function restartPort($port) {
    $this->disablePort($port);
    sleep(1);
    $this->enablePort($port);
  }

  public function disablePort($port){
    snmp2_set($this->ip, SET_COMM, "{$this->statePortOID}.{$port}.100", 'i', '2');
  }

  public function enablePort($port){
    snmp2_set($this->ip, SET_COMM, "{$this->statePortOID}.{$port}.100", 'i', '3');
  }

  public function clearCrcCounters($port){
    snmp2_set($this->ip, SET_COMM, $this->clearCrcOID, 'i', '2');
  }

  public function getConnectStatus() {
    return $this->connectStatus;
  }

  public function getInfoAboutAllPorts() {
    $data = [];
    for ($port = 1; $port < $this->portsCount+1; $port++){
      $data[$port] = $this->getInfoAboutPort($port);
    }
    return $data;
  }

  /**
   * @param $port
   * If you lazy programmer kekeke!
   * return all l2 information about port
   *
   * @return array like
   *  Array
   *  (
   *    [status] => Link-Up
   *    [pair1Status] => Ok
   *    [pair1Length] => 59
   *    [pair2Status] => Ok
   *    [pair2Length] => 59
   *    [description] =>
   *    [crcCount] => 0
   *    [l2Data] => Array
   *      (
   *        [109] => Array
   *          (
   *            [0] => 10:27:be:0f:a1:b9
   *            [1] => 10:27:be:0f:e1:d2
   *            [2] => b0:6e:bf:96:8d:38
   *          )
   *      )
   *    [ip] => 10.196.95.80
   * )
   */
  public function   getInfoAboutPort($port) {
    return [
      'status' => $this->getPortStatus($port),
      'pair1Status' => $this->getPairStatus($port, 1),
      'pair1Length' => $this->getPairLength($port, 1),
      'pair2Status' => $this->getPairStatus($port, 2),
      'pair2Length' => $this->getPairLength($port, 2),
      'description' => $this->getDescription($port),
      'crcCount' => $this->getCrcCount($port),
      'l2Data' => $this->getL2Data($port),
      'ip' => $this->ip,
      'speed' => $this->getSpeed($port)
    ];
  }

  public function getCrcCount($port) {
    $crcCount = snmp2_get($this->ip, READ_COMM, "1.3.6.1.2.1.16.1.1.1.8.{$port}");
    return preg_replace('/^.*:\s?\D*/', '', $crcCount);
  }

  /**
   * @param $port
   * Return array of vlans and mac in these vlans.
   *
   * @return array like
   * Array
   *    (
   *      [109] => Array  Index is VLan ID. It is maybe more than one
   *        (
   *            [0] => 10:27:be:0f:a1:b9
   *            [1] => 10:27:be:0f:e1:d2
   *            [2] => b0:6e:bf:96:8d:38
   *        )
   *    )
   */
  public function getL2Data($port) {
    $response = snmp2_real_walk($this->ip, READ_COMM,'1.3.6.1.2.1.17.7.1.2.2.1.2');
    $data = [];
    foreach ($response as $index => $item) {
      $item = str_replace('INTEGER: ', '', $item);
      if ($item != $port) continue;
      $index = str_replace('iso.3.6.1.2.1.17.7.1.2.2.1.2.', '', $index);
      $octets = preg_split('/\./', $index);
      $vlan = $octets[0];
      unset($octets[0]);
      foreach ($octets as &$octet) {
        $octet = dechex($octet);
        $octet = (strlen($octet) == 1) ? '0'.$octet : $octet;
      }
      $mac = join(':', $octets);
      $data[$vlan][] = $mac;
      break;
    }
    return $data;
  }

  /**
   * @param $port
   * Return port description like a simple string.
   *
   * @return string
   */
  public function getDescription($port) {
    $description = snmp2_get($this->ip, READ_COMM, "1.3.6.1.2.1.31.1.1.1.18.{$port}");
    return str_ireplace('STRING: ', '', str_ireplace('"', '', $description));
  }


  public function getPortStatus($port){
    $status = snmp2_get($this->ip, READ_COMM, "1.3.6.1.4.1.171.12.58.1.1.1.3.{$port}");
    $status = str_ireplace('INTEGER: ', '', $status);
    return ($status == 1) ? 'Link-Up' : 'Link-Down';
  }

  /**
   * @param $port    Port number
   * @param $pairNum Pair nuber
   *                 Method can test 1st and 2nd pairs only!
   *
   * @return string Port's status, if status is strange return 'Other'
   */
  public function getPairStatus($port, $pairNum) {
    switch ($pairNum) {
      case 1:
        $pairOid = 4;
      break;
      case 2:
        $pairOid = 5;
      break;
    }
    $pairStatus = snmp2_get($this->ip, READ_COMM, "1.3.6.1.4.1.171.12.58.1.1.1.{$pairOid}.{$port}");
    $this->log($pairStatus, __FUNCTION__);
    $pairStatus = str_ireplace('INTEGER: ', '', $pairStatus);
    switch ($pairStatus){
      case '0':
        return 'Ok';
      break;
      case '1':
        return 'Open';
      break;
      case '2':
        return 'Short';
      break;
      case '3':
        return 'Open-short';
      break;
      case '4':
        return 'Crosstalk';
      break;
      case '5':
        return 'Unknown';
      break;
      case '6':
        return 'Count';
      break;
      case '7':
        return 'No Cable';
      break;
      default:
        return 'Other';
      break;
    }
  }

  /**
   * @param $port Ports num
   * @param $pairNum Pair num
   * Method can test 1st and 2nd pairs only!
   *
   * @return int Length of wire in meters
   */
  public function getPairLength($port, $pairNum){
    switch ($pairNum) {
      case 1:
        $pairOid = 8;
      break;
      case 2:
        $pairOid = 9;
      break;
    }
    $pairLength = snmp2_get($this->ip, READ_COMM, "1.3.6.1.4.1.171.12.58.1.1.1.{$pairOid}.{$port}");
    $this->log($pairLength, __FUNCTION__);
    $pairLength = str_ireplace('INTEGER: ', '', $pairLength);
    return (integer) $pairLength;
  }

  private function getSpeed($port){
    $speed = snmp2_get($this->ip, READ_COMM, "1.3.6.1.2.1.2.2.1.5.{$port}");
    $speed = str_replace('Gauge32: ', '', $speed);
    return $speed / 1000000;
  }

  private function readModel(){
    $modelString = snmp2_get($this->ip, READ_COMM, '1.3.6.1.2.1.1.1.0');
    if ($modelString) {
      preg_match('/D.S.[0-9]{4}/', $modelString, $sw);
      $this->model = $sw[ 0 ];
    } else {
      $this->connectStatus = FALSE;
    }
  }

  //TODO change to switch $swithModel because we know how many ports in some unit
  private function countOfPorts(){
    $ports = snmp2_walk($this->ip, READ_COMM, '1.3.6.1.2.1.2.2.1.1');
    foreach ($ports as &$port){
      $port = str_replace('INTEGER: ', '', $port);
    }
    $count = count($ports);
    if ($count > 24) {
      $this->portsCount = 24;
    } elseif ($count > 12) {
      $this->portsCount = 12;
    } elseif ($count > 10) {
      $this->portsCount = 10;
    } elseif ($count > 8) {
      $this->portsCount = 8;
    }
  }

  public function testPort($port){
    snmp2_set($this->ip, SET_COMM, "1.3.6.1.4.1.171.12.58.1.1.1.12.{$port}", 'i', '1');
    if($this->debug){
      $status = snmp2_get($this->ip, SET_COMM, "1.3.6.1.4.1.171.12.58.1.1.1.12.{$port}");
      $this->log($status, __FUNCTION__);
    }
  }

  private function startTestingAllPorts(){
    for ($i = 1; $i <= $this->portsCount; $i++) {
      $this->testPort($i);
    }
    sleep(1);
  }

  private function setOids(){
    switch ($this->model) {
      case "DES-3200":
        $this->statePortOID = '1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.3'; //.port.100
        $this->clearCrcOID = '1.3.6.1.4.1.171.11.113.1.5.2.1.2.12.0'; //i 2
      break;
      case "DES-3526":
        $this->statePortOID = '1.3.6.1.4.1.171.11.64.1.2.4.2.1.3'; //port
        $this->clearCrcOID = '1.3.6.1.4.1.171.11.64.1.2.1.2.8.0'; // i 2
      break;
      case "DES-3028":
        $this->statePortOID = '1.3.6.1.4.1.171.11.63.6.2.2.2.1.3'; // .port.100
        $this->clearCrcOID = '1.3.6.1.4.1.171.11.63.6.2.1.2.12.0'; // i 2
      break;
      default:
        $this->statePortOID = NULL;
        $this->clearCrcOID = NULL;
      break;
    }
  }

  private function log($data, $function = '') {
    if ($this->debug) {
      error_log("CLASS SNMP DEBUG: {$function} ". print_r($data, 1));
    }
  }
}