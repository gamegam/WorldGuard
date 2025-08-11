<?php
namespace gamegam\WorldGuardPlugin\Language;
use gamegam\WorldGuardPlugin\Main;

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

	// lang create
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

	public function instantlanguage(){
		$file = $this->api->getConfig()->get("language");
		$folder = $this->api->getDataFolder() . "Language";
		$filePath = $folder . DIRECTORY_SEPARATOR . $file . ".yml";

		if (file_exists($filePath)){
			$this->language = $file;
			$this->translations = yaml_parse_file($filePath);
		}
	}

	public function getString(string $translation, array $variables = []): string{
		$data = $this->translations[$translation] ?? null;
		if ($data == null){
			return "not found: ". $translation;
		}else{
			return $data;
		}
	}

	public function getLanguage(): string
	{
		return $this->language;
	}
}