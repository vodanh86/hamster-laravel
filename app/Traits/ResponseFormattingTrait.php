<?php

namespace App\Traits;

trait ResponseFormattingTrait
{
    private function _formatCountResponse($data, $perPage, $total)
    {
        return [
            'content' => $data,
            'totalElements' => $perPage,
            'totalPages' => $total,
        ];
    }

    private function _formatBaseResponse($statusCode, $data, $message): array
    {
        return [
            'status_code' => $statusCode,
            'data' => $data,
            'message' => $message,
        ];
    }

    private function _formatBaseResponseWithTotal($statusCode, $data,$total, $message): array
    {
        return [
            'status_code' => $statusCode,
            'data' => $data,
            'total' => $total,
            'message' => $message,
        ];
    }
    private function _formatBaseResponseWithTotalAndTotalPage($statusCode, $data, $totalPage, $totalElement, $message)
    {
        return [
            'status_code' => $statusCode,
            'data' => $data,
            'total_pages' => $totalPage,
            'total_elements' => $totalElement,
            'message' => $message,
        ];
    }
}
