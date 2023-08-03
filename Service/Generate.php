<?php

namespace Perspective\ImageAiVariations\Service;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Perspective\ImageAiVariations\Api\GenerateInterface;

class Generate implements GenerateInterface
{
    private UploaderFactory $uploaderFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    private ResultFactory $resultFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private RequestInterface $request;

    /**
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        UploaderFactory $uploaderFactory,
        ResultFactory $resultFactory,
        RequestInterface $request
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->resultFactory = $resultFactory;
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $result = [];
            $file1 = $this->request->getFiles('originalImage');
            $file2 = $this->request->getFiles('canvasImage');
            $result[] = $this->processFile($file1, 'originalImage', $result);
            $result[] = $this->processFile($file2, 'canvasImage', $result);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage()];
        }

        return $result;
    }

    /**
     * @param $file
     * @param mixed $result
     * @return mixed
     * @throws \Exception
     */
    protected function processFile($file, $fileId, $result)
    {

        if ($file && isset($file['tmp_name'])) {
            $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']); // Specify allowed file extensions
            $uploader->setAllowRenameFiles(true); // Allow file renaming if necessary
            $uploader->setFilesDispersion(true);
            @mkdir(BP . '/pub/media/ai_vars', 0777, true);
            $result = $uploader->save(BP . '/pub/media/ai_vars'); // Specify the directory to save the uploaded files
        } else {
            $result = ['error' => 'No file uploaded'];
        }
        return $result;
    }
}
