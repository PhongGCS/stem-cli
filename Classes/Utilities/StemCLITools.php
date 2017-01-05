<?php
/**
 * Created by PhpStorm.
 * User: jong
 * Date: 12/25/16
 * Time: 5:16 PM
 */

namespace ILab\Stem\CommandLine\Utilities;


final class StemCLITools {
	private static $isBedrock = false;
	private static $wordpressPath = null;

	private static $pluginPath = null;
	private static $MUPluginPath = null;
	
	private static $themePath = null;

	private static function resetPaths() {
		self::$isBedrock = false;
		self::$wordpressPath = null;
		self::$themePath = null;
		self::$pluginPath = null;
		self::$MUPluginPath = null;
	}

	static function wordpressPath($currentDir = null) {
		if (self::$wordpressPath != null)
			return self::$wordpressPath;

		self::resetPaths();

		if ($currentDir == null)
			$currentDir = getcwd();

		$currentDir = rtrim($currentDir, DIRECTORY_SEPARATOR);

		if (file_exists($currentDir.DIRECTORY_SEPARATOR.'wp-config.php') || file_exists($currentDir.DIRECTORY_SEPARATOR.'wp-config-sample.php')) {
			self::$wordpressPath = $currentDir.DIRECTORY_SEPARATOR;
			return self::$wordpressPath;
		}

		$subdirs = glob($currentDir.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR);
		foreach($subdirs as $dir) {
			if ($dir == $currentDir)
				continue;

			if (self::wordpressPath($dir)) {
				return self::$wordpressPath;
			}
		}

		return null;
	}

	static function setWordpressPath($wpDir) {
		self::resetPaths();
		self::$wordpressPath = rtrim($wpDir,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
	}

	static function pluginPath() {
		if (self::$pluginPath != null)
			return self::$pluginPath;

		if (self::$wordpressPath == null) {
			if (!self::wordpressPath()) {
				return null;
			}
		}

		$pPath = null;

		if (file_exists(self::$wordpressPath.DIRECTORY_SEPARATOR.'wp-content')) {
			$pPath = self::$wordpressPath.'wp-content'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR;
		} else if (file_exists(self::$wordpressPath.DIRECTORY_SEPARATOR.'app')) {
			self::$isBedrock = true;
			$pPath = self::$wordpressPath.'app'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR;
		}

		if ($pPath)
			self::$pluginPath = $pPath;

		return $pPath;
	}

	static function MUPluginPath() {
		if (self::$MUPluginPath != null)
			return self::$MUPluginPath;

		if (self::$wordpressPath == null) {
			if (!self::wordpressPath(getcwd())) {
				return null;
			}
		}

		$pPath = null;

		if (file_exists(self::$wordpressPath.DIRECTORY_SEPARATOR.'wp-content')) {
			$pPath = self::$wordpressPath.'wp-content'.DIRECTORY_SEPARATOR.'mu-plugins'.DIRECTORY_SEPARATOR;
		} else if (file_exists(self::$wordpressPath.DIRECTORY_SEPARATOR.'app')) {
			self::$isBedrock = true;
			$pPath = self::$wordpressPath.'app'.DIRECTORY_SEPARATOR.'mu-plugins'.DIRECTORY_SEPARATOR;
		}

		if ($pPath)
			self::$MUPluginPath = $pPath;

		return $pPath;
	}

	static function themePath() {
		if (self::$themePath != null)
			return self::$themePath;

		if (self::$wordpressPath == null) {
			if (!self::wordpressPath(getcwd())) {
				return null;
			}
		}

		$pPath = null;

		if (file_exists(self::$wordpressPath.DIRECTORY_SEPARATOR.'wp-content')) {
			$pPath = self::$wordpressPath.'wp-content'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR;
		} else if (file_exists(self::$wordpressPath.DIRECTORY_SEPARATOR.'app')) {
			self::$isBedrock = true;
			$pPath = self::$wordpressPath.'app'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR;
		}

		if ($pPath)
			self::$themePath = $pPath;

		return $pPath;
	}

	static function findWordpressPaths($currentDir) {
		self::resetPaths();

		self::wordpressPath($currentDir);

//		echo "Wordpress: ".self::$wordpressPath."\n";

		if (self::pluginPath()) {
			self::MUPluginPath();
			self::themePath();
		}
//		echo "Plugins: ".self::$MUPluginPath."\n";
	}

	static function isBedrock() {
		return self::$isBedrock;
	}
}