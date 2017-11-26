<?
namespace HalimonAlexander\Sid;
use HalimonAlexander\Sid\Exception\SidRuntimeException;

/**
 * Class Sid
 * Format:
 * NAME = SID
 *
 * @package Core
 * @subpackage Classes
 *
 * @copyright Copyright (c) 2013-2016
 * @author A.Halimon <vvthanatos@gmail.com>
 */
abstract class Sid extends \ReflectionClass
{
  protected static $vocabulary = null;
  protected static $list = [];

  /**
   *
   */
  protected static function extractConstants(){
    $oClass = new \ReflectionClass(get_called_class());
    $constants = $oClass->getConstants();
    unset(
      $constants['IS_IMPLICIT_ABSTRACT'],
      $constants['IS_EXPLICIT_ABSTRACT'],
      $constants['IS_FINAL']
    );

    return $constants;
  }

  /**
   * @param $sid
   * @return bool
   */
  protected static function isHidden($name){
    return $name[0] === '_';
  }

  protected static function hiddenValue($list, $name){
    $_name = $name;
    $name = ltrim($_name, '_');
    $list[$name] = $list[$_name];
    unset($list[$_name]);

    return $list;
  }

  /**
   * @param $lang
   * @return null
   */
  protected static function getVocabulary($lang){
    if (self::$vocabulary !== null)
      return self::$vocabulary;

    $tmp = explode('\\', get_called_class());
    $coreNs = $tmp[0];


    $tmp = "\\{$coreNs}\\Classes\\Vocabulary";
    self::$vocabulary = $tmp::getInstance($lang);

    return self::$vocabulary;
  }
  
  protected static function getClassWithoutNamespace(){
    $tmp = explode('\\', get_called_class());
    return array_pop($tmp);
  }

  /**
   *
   *
   * @param int    $id
   * @param string $lang
   * @param string $context
   * @return mixed
   */
  static function getTitle($id, $lang, $context = 'default')
  {
    $name = self::getNameById($id);
    $class = self::getClassWithoutNamespace();
    $vocabulary = self::getVocabulary($lang);

    return $vocabulary->getTitle($name, $class, $context);
  }

  /**
   * Get full sid list
   *
   * @return array List of values as: Name=>Sid
   */
  static function getList($full = false)
  {
    if ( empty(self::$list) )
      self::$list = self::extractConstants();

    $list = self::$list;
    if (!$full){
      foreach ($list as $name=>$sid)
        if (static::isHidden($name))
          unset($list[$name]);
    }
    else{
      foreach ($list as $name=>$sid)
        if (static::isHidden($name))
          $list = self::hiddenValue($list, $name);
    }

    return $list;
  }

  static function getIdByName($name){
    $list = self::getList();
    $title = strtoupper($name);
    if ( !isset($list[$title]) )
      throw new SidRuntimeException(get_called_class().":{$name} not exists");

    return $list[$name];
  }

  /**
   * Return constant name from SID.
   *
   * @param int $id SID to find
   * @return string Name of constant.
   * @throws SidRuntimeException
   */
  static function getNameById($sid)
  {
    $list = self::getList(true);
    $key = array_search($sid, $list);
    if ($key === false)
      throw new SidRuntimeException('Sid not exists');

    return $key;
  }

  /** Get the number of sids */
  static function getCount($full = false){
    return count(static::getList($full));
  }

  /**
   * Get SID of default Sid
   */
  static function getDefaultSid(){
    $list = static::getList();
    return $list[0];
  }

  /**
   * Get the name of default Sid
   */
  static function getDefaultName(){
    return static::getNameById(static::getDefaultSid());
  }

  /**
   * Returns sid which does not exist. 
   * Can be used in unit test to test invalid id errors.
   */
  static function nx(){
    $fullList = static::getList(true);
    return max( array_values($fullList) ) + 1;
  }
}