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
   * @param string $bucket
   * @param array  $s3arguments
   */
  public function __construct (bucket, array $s3arguments) {
      $this->setBucket($bucket);
      $this->setClient(new S3Client($s3arguments));
  }

  /**
   * @param  object $picture
   * @param  string $fileName
   * @return string file url
   */
  public function uploadImage ($picture, $fileName) {
    $s3 = $this->getClient();
    $bucket = $this->getBucket();
    $strm = fopen($picture, 'rb');
    try {
      $upload = $s3
      ->upload($bucket, 'symfony/' . $fileName, $strm, 'public-read');
    } catch (Aws\S3\Exception\S3Exception $e) {
      return "The error is $e";
    }
    return $upload->get('ObjectURL');
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

}