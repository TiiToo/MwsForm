<?php

namespace Sistema\MWSFORMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Abstract File Base
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class BaseFile
{
    /**
     * @Assert\File()
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="file_path", type="string", length=255, nullable=true)
     */
    private $filePath;

    /**
     *  @var string
     */
    private $temp;

    /**
     * @var string
     */
    private $uploadDir;

    /**
     * Set filePath
     *
     * @param  string $filePath
     * @return File
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Get FilePath
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (is_file($this->getAbsolutePath())) {
            // store the old name to delete after the update
            $this->temp = $this->getAbsolutePath();
        }
        /*else {
            $this->filePath = $file;
        }*/
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return null === $this->filePath
            ? null
            : $this->getUploadRootDir().'/'.$this->filePath;
    }

    public function getWebPath()
    {
        return null === $this->filePath
            ? null
            : $this->getUploadDir().'/'.$this->filePath;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        if (!$this->getUploadDir()) {
            $uploadDir = 'uploads';
        } else {
            $uploadDir = $this->getUploadDir();
        }

        $path = __DIR__ . '/../../../../web/' . $this->getUploadDir();
        if (!file_exists($path)) {
            mkdir($path, 0755);
        }

        return $path;
    }

    public function setUploadDir($uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

    public function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return $this->uploadDir;
    }

    public function getFixturesPath()
    {
        return $this->getAbsolutePath() . 'web/filefixture/';
    }

    //------------------------------------------------------------------------------------------------------//
    /**
     * LifecycleCallbacks
     *
     * @ORM\PreFlush()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->filePath = $filename . '.' . $this->getFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     **/
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->filePath);

        // check if we have an old image
        if (isset($this->temp)) {
            if (file_exists($this->temp)) {
                // delete the old image
                unlink($this->temp);
            }
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
        $this->temp = $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (isset($this->temp)) {
            if (file_exists($this->temp)) {
                unlink($this->temp);
            }
        }
    }

}
