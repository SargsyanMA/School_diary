<?php

/*
 * CKFinder
 * ========
 * http://cksource.com/ckfinder
 * Copyright (C) 2007-2016, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the MIT License.
 * Please read the LICENSE.md file before using, installing, copying,
 * modifying or distribute this file or part of its contents.
 */

namespace CKSource\CKFinder\Plugin\DatabaseAdapter;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use League\Flysystem\Util;
use \PDO;

/**
 * The PDOAdapter class.
 *
 * The Flysystem PDO Database adapter.
 */
class PDOAdapter implements AdapterInterface
{
    use NotSupportingVisibilityTrait;

    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @var string
     */
    protected $table;

    /**
     * The PDOAdapter constructor.
     *
     * @param PDO    $pdo
     * @param string $tableName
     */
    public function __construct(PDO $pdo, $tableName)
    {
        $this->pdo = $pdo;

        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $tableName)) {
            throw new \InvalidArgumentException('Invalid table name');
        }

        $this->table = $tableName;
    }


    public function getPathInfo($path) {
        $arPath = explode('/',$path);

        $fileNameIndex=count($arPath) - 1;
        $fileName=$arPath[$fileNameIndex];
        unset($arPath[$fileNameIndex]);
        $filePath=$_SERVER['DOCUMENT_ROOT'].'/upload/user/'.Cookie::get('userId').'/files/'.$fileName;

        return [
            'filename'=>  $fileName,
            'path' => $filePath
        ];

    }

    /**
     * {@inheritdoc}
     */
    public function write($path, $contents, Config $config)
    {
        $statement = $this->pdo->prepare("INSERT INTO {$this->table} (path, contents, size, type, mimetype, timestamp, realname, user) VALUES(:path, '', :size, :type, :mimetype, :timestamp, :realname, :user)");

        $size = strlen($contents);
        $type = 'file';
        $mimetype = Util::guessMimeType($path, $contents);
        $timestamp = time();

        $pathInfo=$this->getPathInfo($path);

        if ((file_put_contents($pathInfo['path'], $contents)) === false) {
            return false;
        }

        $userId = (int)Cookie::get('userId');

        $statement->bindParam(':path', $path, PDO::PARAM_STR);
       // $statement->bindParam(':contents', $contents, PDO::PARAM_LOB);
        $statement->bindParam(':size', $size, PDO::PARAM_INT);
        $statement->bindParam(':type', $type, PDO::PARAM_STR);
        $statement->bindParam(':mimetype', $mimetype, PDO::PARAM_STR);
        $statement->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
        $statement->bindParam(':realname', $pathInfo['filename'], PDO::PARAM_STR);
        $statement->bindParam(':user', $userId, PDO::PARAM_INT);

        return $statement->execute() ? compact('path', 'contents', 'size', 'type', 'mimetype', 'timestamp', 'realname') : false;
    }


    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $resource, Config $config)
    {
        return $this->write($path, stream_get_contents($resource), $config);
    }

    /**
     * {@inheritdoc}
     */
    public function update($path, $contents, Config $config)
    {
        $statement = $this->pdo->prepare("UPDATE {$this->table} SET mimetype=:mimetype, size=:size WHERE path=:path");

        $size = strlen($contents);
        $mimetype = Util::guessMimeType($path, $contents);

        $pathInfo=$this->getPathInfo($path);

        if ((file_put_contents($pathInfo['path'], $contents)) === false) {
            return false;
        }

        $statement->bindParam(':size', $size, PDO::PARAM_INT);
        $statement->bindParam(':mimetype', $mimetype, PDO::PARAM_STR);
        //$statement->bindParam(':newcontents', $contents, PDO::PARAM_LOB);
        $statement->bindParam(':path', $path, PDO::PARAM_STR);

        return $statement->execute() ? compact('path', 'contents', 'size', 'mimetype') : false;
    }

    /**
     * {@inheritdoc}
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->update($path, stream_get_contents($resource), $config);
    }

    /**
     * {@inheritdoc}
     */
    public function rename($path, $newpath)
    {
        $statement = $this->pdo->prepare("SELECT type FROM {$this->table} WHERE path=:path");
        $statement->bindParam(':path', $path, PDO::PARAM_STR);

        if ($statement->execute()) {
            $object = $statement->fetch(PDO::FETCH_ASSOC);

            if ($object['type'] === 'dir') {
                $dirContents = $this->listContents($path, true);

                $statement = $this->pdo->prepare("UPDATE {$this->table} SET path=:newpath WHERE path=:path");

                $pathLength = strlen($path);

                $statement->bindParam(':path', $currentObjectPath, PDO::PARAM_STR);
                $statement->bindParam(':newpath', $newObjectPath, PDO::PARAM_STR);

                foreach ($dirContents as $object) {
                    $currentObjectPath = $object['path'];
                    $newObjectPath = $newpath . substr($currentObjectPath, $pathLength);

                    $statement->execute();
                }
            }
        }

        $statement = $this->pdo->prepare("UPDATE {$this->table} SET path=:newpath WHERE path=:path");

        $statement->bindParam(':path', $path, PDO::PARAM_STR);
        $statement->bindParam(':newpath', $newpath, PDO::PARAM_STR);

        return $statement->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function copy($path, $newpath)
    {
        $statement = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE path=:path");

        $statement->bindParam(':path', $path, PDO::PARAM_STR);

        if ($statement->execute()) {
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if (!empty($result)) {

                $fileName=$result['realname'];
                $filePath=$_SERVER['DOCUMENT_ROOT'].'/upload/user/'.Cookie::get('userId').'/files/'.$fileName;
                $contents=file_get_contents($filePath);

                $pathInfo=$this->getPathInfo($newpath);

                if ((file_put_contents($pathInfo['path'], $contents)) === false) {
                    return false;
                }

                $statement = $this->pdo->prepare("INSERT INTO {$this->table} (path, contents, size, type, mimetype, timestamp, realname, user) VALUES(:path, '', :size, :type, :mimetype, :timestamp, :realname, :user)");

                $statement->bindParam(':path', $newpath, PDO::PARAM_STR);
                //$statement->bindParam(':contents', $result['contents'], PDO::PARAM_LOB);
                $statement->bindParam(':size', $result['size'], PDO::PARAM_INT);
                $statement->bindParam(':type', $result['type'], PDO::PARAM_STR);
                $statement->bindParam(':mimetype', $result['mimetype'], PDO::PARAM_STR);
                $statement->bindValue(':timestamp', time(), PDO::PARAM_INT);
                $statement->bindParam(':realname', $pathInfo['filename'], PDO::PARAM_STR);
                $statement->bindParam(':user', $result['user'], PDO::PARAM_STR);

                return $statement->execute();
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path)
    {
        $statement = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE path=:path");
        $statement->bindParam(':path', $path, PDO::PARAM_STR);

        if ($statement->execute()) {
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if (!empty($result)) {
                $fileName = $result['realname'];
                $filePath = $_SERVER['DOCUMENT_ROOT'] . '/upload/user/'.Cookie::get('userId').'/files/' . $fileName;
                //unlink($filePath);
            }
        }

        $statement = $this->pdo->prepare("DELETE FROM {$this->table} WHERE path=:path");
        $statement->bindParam(':path', $path, PDO::PARAM_STR);
        return $statement->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDir($dirname)
    {
        $dirContents = $this->listContents($dirname, true);

        if (!empty($dirContents)) {
            $statement = $this->pdo->prepare("DELETE FROM {$this->table} WHERE path=:path");

            $statement->bindParam(':path', $currentObjectPath, PDO::PARAM_STR);

            foreach ($dirContents as $object) {
                $currentObjectPath = $object['path'];
                $statement->execute();
            }
        }

        $statement = $this->pdo->prepare("DELETE FROM {$this->table} WHERE path=:path AND type='dir'");

        $statement->bindParam(':path', $dirname, PDO::PARAM_STR);

        return $statement->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function createDir($dirname, Config $config)
    {
        $userId=Cookie::get('userId');

        $statement = $this->pdo->prepare("INSERT INTO {$this->table} (path, type, timestamp, user) VALUES(:path, :type, :timestamp, :user)");


        $statement->bindParam(':path', $dirname, PDO::PARAM_STR);
        $statement->bindValue(':type', 'dir', PDO::PARAM_STR);
        $statement->bindValue(':timestamp', time(), PDO::PARAM_STR);
        $statement->bindValue(':user', $userId, PDO::PARAM_INT);

        return $statement->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function has($path)
    {
        $statement = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE path=:path AND user=:user");
        $userId=Cookie::get('userId');

        $statement->bindParam(':path', $path, PDO::PARAM_STR);
        $statement->bindValue(':user', $userId, PDO::PARAM_INT);

        if ($statement->execute()) {
            return (bool) $statement->fetch(PDO::FETCH_ASSOC);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function read($path)
    {
        $statement = $this->pdo->prepare("SELECT realname FROM {$this->table} WHERE path=:path AND user=:user");

        $userId=Cookie::get('userId');

        $statement->bindParam(':path', $path, PDO::PARAM_STR);
        $statement->bindValue(':user', $userId, PDO::PARAM_INT);

        if ($statement->execute()) {
            $fileName=$statement->fetch(PDO::FETCH_ASSOC)['realname'];
            $filePath=$_SERVER['DOCUMENT_ROOT'].'/upload/user/'.Cookie::get('userId').'/files/'.$fileName;
            return ['contents'=>file_get_contents($filePath)];

            //return $statement->fetch(PDO::FETCH_ASSOC);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($path)
    {
        $stream = fopen('php://temp', 'w+');
        $result = $this->read($path);

        if (!$result) {
            fclose($stream);

            return false;
        }

        fwrite($stream, $result['contents']);
        rewind($stream);

        return compact('stream');
    }

    /**
     * {@inheritdoc}
     */
    public function listContents($directory = '', $recursive = false)
    {

        $userId=Cookie::get('userId');
        $query = "SELECT path, size, type, mimetype, timestamp FROM {$this->table} WHERE user=:user";

        $useWhere = (bool) strlen($directory);

        if ($useWhere) {
            $query .= " AND path LIKE :path_prefix OR path=:path";
        }

        $statement = $this->pdo->prepare($query);


        $statement->bindParam(':user', $userId, PDO::PARAM_INT);

        if ($useWhere) {
            $pathPrefix = $directory . '/%';
            $statement->bindParam(':path_prefix', $pathPrefix, PDO::PARAM_STR);
            $statement->bindParam(':path', $directory, PDO::PARAM_STR);
        }

        if (!$statement->execute()) {
            return [];
        }

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (empty($result))
            return [];

        $result = array_map(function($v) {
            $v['timestamp'] = (int) $v['timestamp'];
            $v['size'] = (int) $v['size'];
            $v['dirname'] = Util::dirname($v['path']);

            if ($v['type'] === 'dir') {
                unset($v['mimetype']);
                unset($v['size']);
                unset($v['contents']);
            }

            return $v;
        }, $result);

        return $recursive ? $result : Util::emulateDirectories($result);
    }

    /**
     * Get all the metadata of a file or a directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMetadata($path)
    {

        $userId=Cookie::get('userId');

        $statement = $this->pdo->prepare("SELECT id, path, size, type, mimetype, timestamp FROM {$this->table} WHERE path=:path AND user=:user");

        $statement->bindParam(':path', $path, PDO::PARAM_STR);
        $statement->bindValue(':user', $userId, PDO::PARAM_INT);



        if ($statement->execute()) {
            return $statement->fetch(PDO::FETCH_ASSOC);
        }

        return false;
    }

    /**
     * Get all the metadata of a file or a directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getSize($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * Get the MIME type of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMimetype($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * Get the timestamp of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getTimestamp($path)
    {
        return $this->getMetadata($path);
    }


    public function getFileUrl($path)
    {
        $statement = $this->pdo->prepare("SELECT realname FROM {$this->table} WHERE path=:path");

        $statement->bindParam(':path', $path, PDO::PARAM_STR);

        if ($statement->execute()) {
            $filename=$statement->fetch(PDO::FETCH_ASSOC)['realname'];
            return '/upload/user/'.Cookie::get('userId').'/files/'.$filename;
        }

        return false;
    }
}