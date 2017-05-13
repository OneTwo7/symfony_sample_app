<?php

namespace AppBundle\Services;

use Aws\S3\S3Client;

/**
 * Class AmazonS3Service
 *
 * @package AppBundle\Service
 */
class AmazonS3Service {

  /**
   * @var S3Client
   */
  private $client;

  /**
   * @var string
   */
  private $bucket;

  /**
   * @var string
   */
  private $env;

  /**
   * @param string $env
   * @param string $bucket
   * @param array  $s3arguments
   */
  public function __construct ($env, $bucket, array $s3arguments) {
      $this->setEnvironment($env);
      $this->setBucket($bucket);
      $this->setClient(new S3Client($s3arguments));
  }

  /**
   * @param  string $fileName
   * @param  string $content
   * @param  array  $meta
   * @return string file url
   */
  public function upload ($fileName, $content, $meta) {
    return $this->getClient()
    ->upload($this->getBucket(), $fileName, $content, 'public-read', [
      'Metadata' => $meta
    ])->toArray()['ObjectURL'];
  }

  /**
   * @param  object $picture
   * @param  string $fileName
   * @param  string $dir
   * @return string file url
   */
  public function uploadImage ($picture, $fileName, $dir) {
    if ($this->env == "dev") {
      $picture->move($dir, $fileName);
      return $fileName;
    } else {
      $mimeTypeHandler = finfo_open(FILEINFO_MIME_TYPE);
      $meta['contentType'] = finfo_file($mimeTypeHandler, $fileName);
      finfo_close($mimeTypeHandler);
      return $this->upload($fileName, file_get_contents($fileName), $meta);
    }
  }

  /**
   * Getter of client
   *
   * @return S3Client
   */
  protected function getClient () {
      return $this->client;
  }

  /**
   * Setter of client
   *
   * @param S3Client $client
   *
   * @return $this
   */
  private function setClient (S3Client $client) {
      $this->client = $client;
      return $this;
  }

  /**
   * Getter of bucket
   *
   * @return string
   */
  protected function getBucket () {
      return $this->bucket;
  }

  /**
   * Setter of bucket
   *
   * @param string $bucket
   *
   * @return $this
   */
  private function setBucket ($bucket) {
      $this->bucket = $bucket;
      return $this;
  }

  /**
   * Getter of env
   *
   * @return string
   */
  protected function getEnvironment () {
      return $this->env;
  }

  /**
   * Setter of env
   *
   * @param string $env
   *
   * @return $this
   */
  private function setEnvironment ($env) {
      $this->env = $env;
      return $this;
  }

}