<?php

declare(strict_types=1);

namespace Owl\Bundle\FileBundle\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class FilePathExtension extends AbstractExtension
{
    private string $baseFilePath;

    private string $baseUrl;

    public function __construct(string $baseFilePath, RequestStack $requestStack)
    {
        $this->baseFilePath = $baseFilePath;
        $this->baseUrl = '';

        $request = $requestStack->getCurrentRequest();
        if ($request) {
            $this->baseUrl = $request->getSchemeAndHttpHost();
        }
    }

    /**
     * @return list{TwigFilter}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('owl_file_path', [$this, 'getPath']),
        ];
    }

    public function getPath(string $filePath): string
    {
        return $this->baseUrl . '/' . $this->baseFilePath . '/' . $filePath;
    }
}
