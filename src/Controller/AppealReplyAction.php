<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Appeal\AppealReplyWriter;
use App\Controller\Base\AbstractController;
use App\Entity\Appeal;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AppealReplyAction extends AbstractController
{
    public function __invoke(Appeal $data, AppealReplyWriter $appealReplyWriter): Appeal
    {
        $reply = trim((string) $data->getReply());

        if ($reply === '') {
            throw new BadRequestHttpException('Reply text is required.');
        }

        return $appealReplyWriter->write($data, $reply, $this->getUser());
    }
}
