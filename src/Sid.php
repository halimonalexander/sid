<?
/*
 * This file is part of Sid.
 *
 * (c) Halimon Alexander <vvthanatos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace HalimonAlexander\Sid;

use HalimonAlexander\Sid\Exception\VocabularyCallbackNotSet;
use ReflectionClass;
use HalimonAlexander\Sid\Exception\SidItemNotFound;

/**
 * Class Sid
 */
abstract class Sid
{
  /**
   * @var array
   */
  protected static $vocabularyCallbacks = [];
  
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
  
  public static $allowDefaultCallback = true;
  
  public static $defaultCallback = 'self::defaultCallback';
  
  /**
   * Extract constants using ReflectionClass
   */
  private static function extractConstants()
  {
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
  private static function getClassWithoutNamespace()
  {
    return (new ReflectionClass(get_called_class()))->getShortName();
  }
  
  private static function getCallback($language): callable
  {
    if (isset(self::$vocabularyCallbacks[$language]))
      return self::$vocabularyCallbacks[$language];
    
    if (self::$allowDefaultCallback)
      return self::$defaultCallback;
    
    $classname = self::getClassWithoutNamespace();
    
    throw new VocabularyCallbackNotSet("Callback for {$language} is not set. Please use {$classname}::setVocabularyCallback first");
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
  
  protected static function defaultCallback($name, $class, $context)
  {
    echo "{$class}." . strtolower($name);
  }

  /** Get the number of sids */
  public static function getCount($full = false){
    return count(static::getList($full));
  }

  /**
   * Get the name of default Sid
   */
  public static function getDefaultName(){
    return static::getNameById(static::getDefaultSid());
  }

  /**
   * Get SID of default Sid
   */
  public static function getDefaultSid(){
    $list = static::getList();
    return $list[0];
  }

  /**
   * @param $name
   * @return mixed
   * @throws SidItemNotFound
   */
  public static function getIdByName($name){
    $title = strtoupper($name);
    
    $list = self::getList();
    if ( !isset($list[$title]) )
      throw new SidItemNotFound(get_called_class().":{$name} not exists");
    
    // todo Need to consider if to use ReflectionClass instead of arrays
    return $list[$name];
  }

  /**
   * Get full sid list
   *
   * @return array List of values as: Name=>Sid
   */
  public static function getList($full = false)
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
   * @throws SidItemNotFound
   */
  public static function getNameById($sid)
  {
    $list = self::getList(true);
    $key = array_search($sid, $list);
    if ($key === false)
      throw new SidItemNotFound('Sid not exists');

    return $key;
  }

  /**
   * Get Sid title from vocabulary
   *
   * @param int    $id
   * @param string $lang
   * @param string $context
   * 
   * @return mixed
   */
  public static function getTitle($id, $lang, $context = 'default')
  {
    $name = self::getNameById($id);
    $class = self::getClassWithoutNamespace();

    return call_user_func(self::getCallback($lang), $name, $class, $context);
  }

  /**
   * Set vocabulary callback function
   *
   * @param $lang string
   * @param $vocabularyCallback callable
   */
  public static function setVocabularyCallback(string $language, callable $vocabularyCallback)
  {
    self::$vocabularyCallbacks[ $language ] = $vocabularyCallback;
  }

  /**
   * Returns sid which does not exist. 
   * Can be used in unit test to test invalid id errors.
   */
  public static function nx(){
    $fullList = static::getList(true);
    
    return max( array_values($fullList) ) + 1;
  }
}