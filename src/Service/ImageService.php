<?php

namespace App\Services;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class ImageService extends AbstractController
{
    public function moveImage(UploadedFile $file, $photo): void
    {
        $upload_dir = $this->getParameter('upload_dir');
        $image = md5(uniqid()) . '.' . $file->guessExtension();
        $file->move($upload_dir, $image);
        $photo->setImage($image);
    }

    public function deleteImage($photo): void
    {
        $upload_dir = $this->getParameter('upload_dir');
        $image = $photo->getImage();
        $oldimage = $upload_dir . '/' . $image;

        if (file_exists($oldimage)) {
            unlink($oldimage);
        }
    }

    public function updateImage(UploadedFile $file, $photo): void
    {
        $this->deleteImage($photo);
        $this->moveImage($file, $photo);
    }
}
