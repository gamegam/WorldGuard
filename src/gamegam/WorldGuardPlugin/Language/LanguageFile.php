<?php
namespace gamegam\WorldGuardPlugin\Language;
use gamegam\WorldGuardPlugin\Main;
use pocketmine\Server;

class LanguageFile {

	private Main $api;
	public $translations;
	private $list = [
		"en-US",
		"ko-KR"
	];

	public $language;
	public function __construct(Main $api)
	{
		$this->api = $api;
	}

	/**
	public function Create_File(string $file, $dev = false)
	{
		$folder = $this->api->getDataFolder() . "Language";
		@mkdir($folder);
		foreach ($this->list as $guard) {
			$resourcePath = "Language" . DIRECTORY_SEPARATOR . $guard . ".yml";
			$this->api->saveResource($resourcePath, $dev);


		}
		$filePath = $folder . DIRECTORY_SEPARATOR . $file . ".yml";

		if (file_exists($filePath)){
			$this->language = $file;
			$this->translations = yaml_parse_file($filePath);
		}
	}
	 * **/

	public function Create_File(string $file, $dev = false)
	{
		$folder = $this->api->getDataFolder() . "Language";
		if(!is_dir($folder)) @mkdir($folder);

		foreach ($this->list as $guard) {
			$resourcePath = "Language" . DIRECTORY_SEPARATOR . $guard . ".yml";
			$outPath = $this->api->getDataFolder() . $resourcePath;

			if (!file_exists($outPath)) {
				$this->api->saveResource($resourcePath, $dev);
			} else {
				$resourceStream = $this->api->getResource($resourcePath);
				if ($resourceStream === null) continue;
				$resourceContent = stream_get_contents($resourceStream);
				$newData = yaml_parse($resourceContent);
				fclose($resourceStream);

				$existingContent = file_get_contents($outPath);
				$existingData = yaml_parse($existingContent);
				$resourceKeys = array_keys($newData);
				$lines = explode("\n", $existingContent);

				foreach ($resourceKeys as $index => $key) {
					if (!isset($existingData[$key])) {
						$value = $newData[$key];
						$escapedValue = str_replace("\n", "\\n", $value);
						$newLine = "{$key}: {$escapedValue}";

						if ($index === 0) {
							array_unshift($lines, $newLine);
						} else {
							$prevKey = $resourceKeys[$index - 1];
							$inserted = false;
							foreach ($lines as $i => $line) {
								if (preg_match('/^' . preg_quote($prevKey, '/') . '\s*:/', $line)) {
									array_splice($lines, $i + 1, 0, $newLine);
									$inserted = true;
									break;
								}
							}
							if (!$inserted) $lines[] = $newLine;
						}
						$existingData[$key] = $value;
					}
				}
				file_put_contents($outPath, implode("\n", $lines));
			}
		}

		$filePath = $folder . DIRECTORY_SEPARATOR . $file . ".yml";
		if (file_exists($filePath)){
			$this->language = $file;
			$this->translations = yaml_parse_file($filePath);
		}
	}

	public function getLoadedLanguage(): string
	{
		$lang = $this->api->getConfig()->get("language");
		$filePath = $this->api->getDataFolder() . "Language" . DIRECTORY_SEPARATOR . $lang . ".yml";

		if (file_exists($filePath)) {
			$lastModified = filemtime($filePath);

			if (!isset($this->lastLoadTime) || $lastModified > $this->lastLoadTime) {
				$this->translations = yaml_parse_file($filePath);
				$this->lastLoadTime = $lastModified;
				$this->language = $lang;
			}
		}

		return $lang;
	}

	public function getString(string $translation): string
	{
		$this->getLoadedLanguage();

		$data = $this->translations[$translation] ?? null;
		if ($data === null) {
			return "not found: " . $translation;
		}
		return $data;
	}

	public function getLanguage(): string
	{
		return $this->language;
	}
}