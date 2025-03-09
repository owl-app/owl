<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Owl\Component\File\Generator;

use Owl\Component\File\Model\FileInterface;
use ReflectionClass;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadedFilePathGenerator implements FilePathGeneratorInterface
{
    public function generate(FileInterface $image): string
    {
        /** @var UploadedFile $file */
        $file = $image->getFile();
        $reflectionSubject = new ReflectionClass($image->getFileSubject());

        $hash = bin2hex(random_bytes(16));
        $createdAt = $image->getCreatedAt()->format('Y/m/d');

        return $this->expandPath($createdAt, $hash . '.' . $file->guessExtension(), $reflectionSubject->getShortName());
    }

    private function expandPath(string $createdAt, string $path, string $subjectClass): string
    {
        return sprintf('%s/%s/%s', $createdAt, $this->dirFromaCamelCase($subjectClass), substr($path, 4));
    }

    private function dirFromaCamelCase(string $input): string
    {
        $pattern = '!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!';
        preg_match_all($pattern, $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ?
                  strtolower($match) :
                lcfirst($match);
        }

        return implode('_', $ret);
    }
}
