<?php
namespace UPhp\helpers;

use \ZipArchive;

class MakeZip
{
    /**
     * Add files and sub-directories in a folder to zip file.
     * @param string $folder
     * @param ZipArchive $zipFile
     * @param int $exclusiveLength Number of text to be exclusived from the file path.
     */
    private static function folderToZip($folder, &$zipFile, $exclusiveLength) {
        $handle = opendir($folder);
        while (false !== $f = readdir($handle)) {
            if ($f != '.' && $f != '..') {
                $filePath = "$folder/$f";
                // Remove prefix from file path before add to zip.
                $localPath = substr($filePath, $exclusiveLength);
                if (is_file($filePath)) {
                    $zipFile->addFile($filePath, $localPath);
                } elseif (is_dir($filePath)) {
                    // Add sub-directory.
                    $zipFile->addEmptyDir($localPath);
                    self::folderToZip($filePath, $zipFile, $exclusiveLength);
                }
            }
        }
        closedir($handle);
    }

    /**
     * Zip a folder (include itself).
     * Usage:
     *   HZip::zipDir('/path/to/sourceDir', '/path/to/out.zip');
     *
     * @param string $sourcePath Path of directory to be zip.
     * @param string $outZipPath Path of output zip file.
     * @param bool $deleteOriginal Deleta a pasta original, default false
     * @return mixed
     */
    public static function zipDir($sourcePath, $outZipPath, $deleteOriginal = false)
    {
        $pathInfo = pathInfo($sourcePath);
        $parentPath = $pathInfo['dirname'];
        $dirName = $pathInfo['basename'];

        $z = new ZipArchive();

        $ret = $z->open($outZipPath, ZipArchive::OVERWRITE);
        if ($ret === ZipArchive::ER_NOENT)
            $z->open($outZipPath, ZipArchive::CREATE);
        elseif ($ret !== ZipArchive::ER_MULTIDISK) return self::zipStatusString($ret);

        $z->addEmptyDir($dirName);
        self::folderToZip($sourcePath, $z, strlen("$parentPath/"));
        $z->close();

        self::delTree($sourcePath, $deleteOriginal);
    }

    /**
     * Compacta arquivos no formato .zip
     * Exemplo:
     *      $filePath = 'download/temp/';
     *      $outZipPath = 'download/nameFile.zip';
     *      $prefix = 'img_';
     *
     *      # O parametro $files pode receber um array com sub arrays conforme segue o exemplo abaixo, ou pode receber um array contendo diretamente as extensões, ex.: ['png', 'jpg', ...]
     *      $files = ['files' => ['file1.jpg', 'file2.jpg], 'extensions' => ['png', 'gif']];
     *
     *      # $deleteOriginals por default é TRUE
     *      $deleteOriginals = false;
     *
     *      # $removeAllPath por default é TRUE
     *      $removeAllPath = false;
     *
     *      HZip::zipFiles($filesPath, $outZipPath, $prefix, $files, $deleteOriginals, $removeAllPath);
     *
     * @param string $filesPath Caminho dos arquivos que serão compactados.
     * @param string $outZipPath Caminho onde será armazenado o arquivo Zip.
     * @param string $prefix String que precede o nome do arquivo.
     * @param mixed $files Array para especificar extensões ou arquivos, ou um valor empty todos.
     * @param bool $deleteOriginals Chave para informar se serão deletados os arquivos originais.
     * @param bool $removeAllPath Chave para informar se será deletado o caminho até os arquivos originais.
     * @return mixed Retorna TRUE caso tenha sucesso ou uma string de erro.
     */
    public static function zipFiles($filesPath, $outZipPath, string $prefix = '.', $files = null, $deleteOriginals = true, $removeAllPath = true)
    {
        # Força o carregamente de todos os arquivos caso seja empty()
        if (empty($files)) $files = ['[a-z]+'];
        elseif (! is_array($files) && ! empty($files)) return self::zipStatusString(ZipArchive::ER_NOENT);

        $pattern = '';
        $z = new ZipArchive();

        $ret = $z->open($outZipPath, ZipArchive::OVERWRITE);
        if ($ret === ZipArchive::ER_NOENT) {
            $ret = $z->open($outZipPath, ZipArchive::CREATE);
            if ($ret !== true) return self::zipStatusString($ret);
        } elseif ($ret !== true) return self::zipStatusString($ret);

        $options = ['add_path' => $prefix, 'remove_all_path' => boolval($removeAllPath)];

        if (isset($files['files']))
            $pattern = implode("|", $files['files']);

        if (isset($files['extensions'])) {
            $sfiles = implode("|", $files['extensions']);
            if (empty($pattern)) $pattern = '\.(?:' . $sfiles . ')';
            else $pattern .= '|\.(?:' . $sfiles . ')';
        } elseif (! isset($files['files']) && ! isset($files['extensions'])) {
            $sfiles = implode("|", $files);
            $pattern = '\.(?:' . $sfiles . ')';
        }

        $ret = $z->addPattern('/' . $pattern . '$/', $filesPath, $options);
        if (! $ret) {
            return self::zipStatusString(ZipArchive::ER_WRITE);
        }

        $ret = $z->close();
        if ($ret !== true) return self::zipStatusString($ret);

        return self::deleteFiles($filesPath, $files, $deleteOriginals);
    }

    public static function zipStatusString($status)
    {
        switch( (int) $status ) {
            case ZipArchive::ER_OK           : return 'No error';
            case ZipArchive::ER_MULTIDISK    : return 'Multi-disk zip archives not supported';
            case ZipArchive::ER_RENAME       : return 'Renaming temporary file failed';
            case ZipArchive::ER_CLOSE        : return 'Closing zip archive failed';
            case ZipArchive::ER_SEEK         : return 'Seek error';
            case ZipArchive::ER_READ         : return 'Read error';
            case ZipArchive::ER_WRITE        : return 'Write error';
            case ZipArchive::ER_CRC          : return 'CRC error';
            case ZipArchive::ER_ZIPCLOSED    : return 'Containing zip archive was closed';
            case ZipArchive::ER_NOENT        : return 'No such file';
            case ZipArchive::ER_EXISTS       : return 'File already exists';
            case ZipArchive::ER_OPEN         : return 'Can\'t open file';
            case ZipArchive::ER_TMPOPEN      : return 'Failure to create temporary file';
            case ZipArchive::ER_ZLIB         : return 'Zlib error';
            case ZipArchive::ER_MEMORY       : return 'Malloc failure';
            case ZipArchive::ER_CHANGED      : return 'Entry has been changed';
            case ZipArchive::ER_COMPNOTSUPP  : return 'Compression method not supported';
            case ZipArchive::ER_EOF          : return 'Premature EOF';
            case ZipArchive::ER_INVAL        : return 'Invalid argument';
            case ZipArchive::ER_NOZIP        : return 'Not a zip archive';
            case ZipArchive::ER_INTERNAL     : return 'Internal error';
            case ZipArchive::ER_INCONS       : return 'Zip archive inconsistent';
            case ZipArchive::ER_REMOVE       : return 'Can\'t remove file';
            case ZipArchive::ER_DELETED      : return 'Entry has been deleted';

            default: return sprintf('Unknown status %s', $status );
        }
    }

    private static function deleteFiles($filesPath, $files, $delete = false)
    {
        if (empty($delete)) return true;

        $ret = true;
        if (empty($files)) {
            $files = array_diff(scandir($filesPath), array('.','..'));
            foreach ($files as $file) {
                if (is_dir($filesPath . $file)) $ret &= self::delTree($filesPath . $file);
                else $ret &= unlink($filesPath . $file);

                if (! $ret) return self::zipStatusString(ZipArchive::ER_REMOVE);
            }
        } else {
            $extensions = null;
            $fileName = null;
            if (isset($files['files'])) $fileName = $files['files'];

            if (isset($files['extensions'])) $extensions = $files['extensions'];
            elseif (! isset($files['files']) && ! isset($files['extensions'])) $extensions = $files;

            if (is_dir($filesPath)) {
                if ($dh = opendir($filesPath)) {
                    while (($file = readdir($dh)) !== false) {
                        if (! empty($extensions) && in_array(pathinfo($file)['extension'], $extensions)) {
                            $ret &= unlink($filesPath . $file);
                            if (! $ret) return self::zipStatusString(ZipArchive::ER_REMOVE);
                        }
                        if (! empty($fileName) && in_array(pathinfo($file)['basename'], $fileName)) {
                            $ret &= unlink($filesPath . $file);
                            if (! $ret) return self::zipStatusString(ZipArchive::ER_REMOVE);
                        }
                    }
                    closedir($dh);
                }
            }
        }
        return $ret;
    }

    private static function delTree($dir, $delete = true)
    {
        if (empty($delete)) return true;

        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir($dir . $file)) ? delTree($dir . $file) : unlink($dir . $file);
        }
        return rmdir($dir);
    }

    private static function downloadZip($zipName)
    {
        ///Then download the zipped file.
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$zipName);
        header('Content-Length: ' . filesize($zipName));
        readfile($zipName);
    }
}