<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

/**
 * API response formatları için ortak metodlar
 */
trait ApiResponse
{
    /**
     * Başarılı response
     */
    protected function successResponse(
        $data = null,
        string $message = 'İşlem başarılı',
        int $code = 200
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Hata response
     */
    protected function errorResponse(
        string $message = 'Bir hata oluştu',
        int $code = 400,
        $errors = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Bulunamadı response
     */
    protected function notFoundResponse(string $message = 'Kayıt bulunamadı'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Yetkisiz erişim response
     */
    protected function unauthorizedResponse(string $message = 'Yetkisiz erişim'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * Yasak erişim response
     */
    protected function forbiddenResponse(string $message = 'Bu işlem için yetkiniz yok'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * Validasyon hatası response
     */
    protected function validationErrorResponse(array $errors, string $message = 'Doğrulama hatası'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * Sync/Batch işlem sonucu response
     */
    protected function syncResponse(
        int $created,
        int $updated,
        array $errors = [],
        string $message = 'Senkronizasyon tamamlandı'
    ): JsonResponse {
        return response()->json([
            'success' => empty($errors),
            'message' => $message,
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors,
        ]);
    }

    /**
     * Silme işlemi response
     */
    protected function deletedResponse(string $message = 'Kayıt başarıyla silindi'): JsonResponse
    {
        return $this->successResponse(null, $message);
    }
}
