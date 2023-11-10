<?php

namespace Faid\Cache\Engine {

    use \Faid\Cache\Exception;
    use \Faid\Configure\Configure;

    class FileCache implements CacheEngineInterface
    {

        const ConfigurePath = 'SimpleCache.FileCache';

        protected $basePath = '';
        protected $lastLoadedPath = '';
        protected $lastLoadedData = array();

        /**
         * Создает кеш и сохраняет его на файловую систему
         *
         * @param string $key ключ хеша
         * @param mixed $data данные для хеширования
         */
        public function set($key, $data, $timeActual = 0)
        {
            // Проверяем ключ на пустоту

            if (empty($key) or preg_match('#..\/.\/#', $key)) {
                throw new Exception('Invalid cache name: '. $key);
            }

            // Создаем файл
            $path = $this->getPath($key);

            if (!empty($timeActual)) {
                $timeActual = time() + $timeActual;
            }
            $data = array(
                'expire' => $timeActual,
                'data' => $data
            );
            $data = serialize($data);

            file_put_contents($path, $data, LOCK_EX);
        }

        /**
         * Синоним метода load
         *
         * @param string $key
         */
        public function get($key)
        {
            $path = $this->getPath($key);
            $this->loadData($path);
            if (!$this->testIfCurrentCacheActual()) {
                throw new Exception('Cache "' . $key . '" not actual');
            }
            return $this->lastLoadedData['data'];
        }

        protected function loadData($path)
        {
            $isReadable = file_exists($path) && is_readable($path);
            if (!$isReadable) {
                throw new Exception(sprintf('Path %s not found or not readable', $path));
            }
            $validator = new \Faid\Validators\FileInSecuredFolder($this->basePath);
            if (!$validator->isValid($path)) {
                throw new Exception('File restricted by security settings: ' . $path);
            }
            $data = file_get_contents($path);
            $this->lastLoadedPath = $path;
            $this->lastLoadedData = unserialize($data);
        }

        /**
         * Удаляет кеш
         *
         * @param string $key
         */
        public function clear($key)
        {
            $path = self::getPath($key);
            if (file_exists($path) && is_file($path)) {
                unlink($path);
            } else {
                throw new Exception('Path `' . $key . '` isn`t file');
            }
        }


        public function isActual($key)
        {
            $path = $this->getPath($key);
            try {
                $this->loadData($path);
            } catch (Exception $e) {
                return false;
            }
            return $this->testIfCurrentCacheActual();

        }

        protected function testIfCurrentCacheActual()
        {
            if (!empty($this->lastLoadedData['expire'])) {
                if (time() >= $this->lastLoadedData['expire']) {
                    return false;
                }
            }

            return true;
        }

        protected function getPath($key)
        {
            $path = $this->basePath . $key;
            return $path;
        }

        public function __construct()
        {
            $key = self::ConfigurePath . '.BaseDir';
            $this->basePath = Configure::read($key);
        }
    }

}
?>
