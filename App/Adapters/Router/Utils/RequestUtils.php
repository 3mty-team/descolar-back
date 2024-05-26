<?php

namespace Descolar\Adapters\Router\Utils;

class RequestUtils
{

    public static function cleanBody(): void
    {
        //From : https://stackoverflow.com/a/18678678. Edited by Mehdi A.
        $reqData = fopen("php://input", "r");

        $rawData = '';

        while ($chunk = fread($reqData, 1024))
            $rawData .= $chunk;

        fclose($reqData);

        $boundary = substr($rawData, 0, strpos($rawData, "\r\n"));

        if (empty($boundary)) {
            parse_str($rawData, $data);
            $GLOBALS['_REQ'] = $data;
            return;
        }

        $parts = array_slice(explode($boundary, $rawData), 1);
        $data = array();

        foreach ($parts as $part) {
            if ($part == "--\r\n") break;

            $part = ltrim($part, "\r\n");
            list($rawHeaders, $body) = explode("\r\n\r\n", $part, 2);

            $rawHeaders = explode("\r\n", $rawHeaders);
            $headers = array();
            foreach ($rawHeaders as $header) {
                list($name, $value) = explode(':', $header);
                $headers[strtolower($name)] = ltrim($value, ' ');
            }

            if (isset($headers['content-disposition'])) {
                preg_match(
                    '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
                    $headers['content-disposition'],
                    $matches
                );
                list(, $type, $name) = $matches;

                if (isset($matches[4])) {
                    if (isset($_FILES[$matches[2]])) {
                        continue;
                    }

                    $filename = $matches[4];

                    $filename_parts = pathinfo($filename);
                    $tmp_name = tempnam(ini_get('upload_tmp_dir'), $filename_parts['filename']);

                    $_FILES[$matches[2]] = array(
                        'error' => 0,
                        'name' => $filename,
                        'tmp_name' => $tmp_name,
                        'size' => strlen($body),
                        'type' => $value
                    );

                   file_put_contents($tmp_name, $body);
                }
                else {
                    $data[$name] = substr($body, 0, strlen($body) - 2);
                }
            }
        }

        $GLOBALS['_REQ'] = $data;
    }

}