<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 10/12/17
 * Time: 12:26
 */

namespace App\Interfaces;

/**
 * Interface UploadableInterface
 *
 * Doctrine event listen this Interface to handle upload file
 */
interface UploadableInterface
{
    /**
     * @return string|null
     */
    public function getRawContent(): ?string;

    /**
     * @param string $rawContent
     */
    public function setRawContent(string $rawContent);

    /**
     * @return string|null
     */
    public function getExternalLink(): ?string;

    /**
     * @return string|null
     */
    public function getFileName(): ?string;

    /**
     * @param string $filename
     */
    public function setFileName(string $filename);

    /**
     * @return string|null
     */
    public function getLocation(): ?string;

    /**
     * @param string $location
     */
    public function setLocation(string $location);
}
