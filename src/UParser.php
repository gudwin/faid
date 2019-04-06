<?php

namespace Faid;

use Faid\Configure\Configure;
use InvalidArgumentException;

class UParser extends StaticObservable
{
    static protected function includeFile($path, $variables)
    {
        if (!empty($viewVariables)) {
            extract($viewVariables);
        }
        $result = include $path;
        $content = ob_get_contents();

        if (!empty($content)) {
            $result = $content . $result;
        }
        return $result;
    }

    static protected function getOutputBuffer()
    {
        $contents = ob_get_contents();
        if (sizeof(ob_list_handlers()) > 0) {
            ob_clean();
        } else {
            ob_start();
        }
        return $contents;
    }

    static protected function flushOutputBufferAndOutput($contents)
    {
        ob_clean();
        print $contents;
    }

    /**
     * @param $templateFile
     * @param array $variables
     * @return mixed|string
     */
    static public function parsePHPFile($templateFile, $variables = [])
    {
        $oldContents = static::getOutputBuffer();
        $result = static::includeFile($templateFile, $variables);
        static::flushOutputBufferAndOutput($oldContents);
        return $result;
    }

    /**
     * @param string $code
     * @param array $variables
     * @return string
     */
    static public function parsePHPCode($code, $variables = null)
    {
        $baseDir = Configure::read('UParser.tmp_dir');
        $path = $baseDir . uniqid();
        file_put_contents($path, $code);
        $result = static::parsePHPFile($path, $variables);
        unlink($path);
        return $result;
    }
}
