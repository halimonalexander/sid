<?
namespace HalimonAlexander\Sid;

use HalimonAlexander\Sid\Exception\SidRuntimeException;

/**
 * Class Sid
 */
abstract class Sid{

  /** @var null */
  protected static $vocabulary = null;

  /** @var array As list [name => value] */
  protected static $list = [];

  /**
   * @var string
   */
  protected static $commonSidNamePattern = '^[A-Za-z][a-zA-Z0-9_-]*$';

  /**
   * @var string
   */
  protected static $hiddenSidNamePattern = '^_([A-Za-z][a-zA-Z0-9_-]*)$';

  /**
   * Extract constants using ReflectionClass
   */
  private static function extractConstants(){
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
   * @return string
   */
  private static function getClassWithoutNamespace(){
    return (new \ReflectionClass(get_called_class()))->getShortName();
  }

  /**
   * @return string
   */
  private static function getNamespace(){
    return (new \ReflectionClass(get_called_class()))->getNamespaceName();
  }

  /**
   * Get vocabulary instance
   *
   * @param $lang string
   * @return null
   */
  private static function getVocabulary($lang){
    if (self::$vocabulary === null){
      $vocabularyClass = self::getVocabularyClass();
      self::$vocabulary = $vocabularyClass::getInstance($lang);
    }
    return self::$vocabulary;
  }

  /**
   * Check if item is common
   *
   * @param $name
   * @return bool
   */
  private static function isCommon($name){
    return !!preg_match("/".self::$commonSidNamePattern."/", $name);
  }

  /**
   * Check if item is hidden
   *
   * @param $name
   * @return bool
   */
  private static function isHidden($name){
    return !!preg_match("/".self::$hiddenSidNamePattern."/", $name, $matches);
  }

  /**
   * Check if item is valid
   *
   * @param $name
   * @return bool
   */
  private static function isValid($name){
    return self::isHidden($name) || self::isCommon($name);
  }

  /**
   * @return string Vocabulary class name
   */
  protected static function getVocabularyClass(){
    $namespace = self::getNamespace();
    $vocabularyClass = "{$namespace}\\Vocabulary";
    if ( !class_exists($vocabularyClass) )
      throw new SidRuntimeException('Vocabulary class not exists');

    return $vocabularyClass;
  }

  /**
   * Update hidden value's name by removing leading _
   * 
   * @param $list
   * @param $name
   * @return mixed
   */
  protected static function updateHiddenValue($list, $name){
    $sid = $list[$name];
    unset($list[$name]);

    preg_match("/".self::$hiddenSidNamePattern."/", $name, $match);
    $list[ $match[1] ] = $sid;

    return $list;
  }

  /** Get the number of sids */
  static function getCount($full = false){
    return count(static::getList($full));
  }

  /**
   * Get the name of default Sid
   */
  static function getDefaultName(){
    return static::getNameById(static::getDefaultSid());
  }

  /**
   * Get SID of default Sid
   */
  static function getDefaultSid(){
    $list = static::getList();
    return $list[0];
  }

  /**
   * @param $name
   * @return mixed
   */
  static function getIdByName($name){
    $list = self::getList();
    $title = strtoupper($name);
    if ( !isset($list[$title]) )
      throw new SidRuntimeException(get_called_class().":{$name} not exists");
    // todo Need to consider if to use ReflectionClass instead of arrays
    return $list[$name];
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

    foreach (self::$list as $name=>$sid)
      if ( !self::isValid($name) )
        unset(self::$list[$name]);

    $list = self::$list;
    if (!$full){
      foreach ($list as $name=>$sid)
        if (static::isHidden($name))
          unset($list[$name]);
    }
    else{
      foreach ($list as $name=>$sid)
        if (static::isHidden($name))
          $list = self::updateHiddenValue($list, $name);
    }

    return $list;
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

  /**
   * Get Sid title from vocabulary
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
   * Returns sid which does not exist. 
   * Can be used in unit test to test invalid id errors.
   */
  static function nx(){
    $fullList = static::getList(true);
    return max( array_values($fullList) ) + 1;
  }
}