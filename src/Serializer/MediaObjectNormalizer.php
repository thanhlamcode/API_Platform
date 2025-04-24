<?php
// api/src/Serializer/MediaObjectNormalizer.php

namespace App\Serializer;

use App\Entity\MediaObject;
use Vich\UploaderBundle\Storage\StorageInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MediaObjectNormalizer implements NormalizerInterface
{
    // Để tránh việc gọi normalize nhiều lần trên cùng đối tượng
    private const ALREADY_CALLED = 'MEDIA_OBJECT_NORMALIZER_ALREADY_CALLED';

    public function __construct(
        #[Autowire(service: 'api_platform.jsonld.normalizer.item')] // Chỉ định service normalizer chuẩn của API Platform
        private readonly NormalizerInterface $normalizer,

        private readonly StorageInterface $storage // Dùng để lấy URL của tệp
    ) {
    }

    public function normalize($object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        // Kiểm tra nếu đã gọi normalize trước đó để tránh lỗi vô hạn
        if (isset($context[self::ALREADY_CALLED])) {
            return $object;  // Trả về đối tượng nguyên vẹn nếu đã gọi normalize trước đó
        }

        // Đánh dấu rằng normalize đã được gọi
        $context[self::ALREADY_CALLED] = true;

        // Lấy URL của tệp đã tải lên
        $object->contentUrl = $this->storage->resolveUri($object, 'file');

        // Tiến hành normalize đối tượng thông qua normalizer mặc định của API Platform
        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        // Kiểm tra xem đối tượng có phải là MediaObject và có yêu cầu hỗ trợ normalize không
        if (isset($context[self::ALREADY_CALLED])) {
            return false; // Không gọi normalize lại trên đối tượng đã được xử lý
        }

        return $data instanceof MediaObject; // Chỉ hỗ trợ normalize với MediaObject
    }

    public function getSupportedTypes(?string $format): array
    {
        // Chỉ hỗ trợ normalize cho lớp MediaObject
        return [
            MediaObject::class => true,
        ];
    }
}
