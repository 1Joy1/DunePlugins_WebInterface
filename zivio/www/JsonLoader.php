<?php
/**
 * Class JsonLoader | JsonLoader.php
 */

/**
 * Загружает конфигурационные данные из файла JSON
 *
 * @author Marshak Igor aka !Joy!
 * @package Any
 * @version v.1.0 (11.09.17)
 * @copyright Copyright (c) 2017 Marshak Igor aka !Joy!
 *
 */
class JsonLoader
{

    /**
     * Объект получаемый из файла JSON при помощи json_decode()
     *
     * @var stdClass
     */
    protected $config;



    /**
     * Конструктор. Принимает путь к JSON файлу,
     * вызывает метод проверки наличия файла и метод получения конфига.
     *
     * @param string $file
     * @return void
     */
    public function __construct($file) {

        $this->checkAvailableFile($file);

        $this->createConfigFromFile($file);
    }


    /**
     * Проверка наличия файла
     *
     * @param string $file
     * @throws ConfigLoader_Exception
     * @return void
     */
    protected function checkAvailableFile($file) {

        if (!file_exists($file)) {

            throw new Exception("Ошибка загрузки конфигурационного файла. Файл $file отсутствует.");
        }
    }


    /**
     * Создание объекта с конфигом
     *
     * @param string $file
     * @throws ConfigLoader_Exception
     * @return void
     */
    protected function createConfigFromFile($file) {

        $this->config = json_decode(file_get_contents($file));


        if (json_last_error() !== JSON_ERROR_NONE) {

            throw new Exception("Ошибка парсера JSON. Конфигурационный файл $file должен быть JSON");
        }

    }


    /**
     * Возвращает stdClass
     *
     * @return stdClass
     */
    public function getConfig() {

        return $this->config;
    }

}