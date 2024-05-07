<?php
/**
 * @copyright Copyright © Avarda. All rights reserved.
 * @package   Avarda_Checkout3
 */

namespace Avarda\Checkout3\Gateway\Client\Converter;

use Exception;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Webapi\Exception as WebapiException;
use Magento\Payment\Gateway\Http\ConverterException;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Psr\Http\Message\ResponseInterface;

class JsonConverter implements ConverterInterface
{
    /**
     * Converts gateway response to array structure
     *
     * @param ResponseInterface $response
     * @return array
     * @throws ConverterException
     * @throws WebapiException
     * @throws AuthorizationException
     */
    public function convert($response)
    {
        try {
            $body = json_decode((string)$response->getBody(), true);
        } catch (Exception $e) {
            throw new ConverterException(
                __('Something went wrong with Avarda Checkout. Please try again later.')
            );
        }

        if ($response->getStatusCode() === WebapiException::HTTP_UNAUTHORIZED) {
            throw new AuthorizationException(
                __('Failed to authorize Avarda Checkout.')
            );
        }

        if (isset($body['errorMessage'])) {
            throw new WebapiException(__($body['errorMessage']));
        }

        if (isset($body['Errors'])) {
            $errorMsg = '';
            foreach ($body['Errors'] as $error) {
                $errorMsg .= __($error['errorMessage']) . ' ';
            }
            throw new WebapiException(__($errorMsg));
        }

        if ($response->getStatusCode() == 400) {
            throw new WebapiException(__('Something went wrong with Avarda Checkout. Please try again later.'));
        }

        return $body;
    }
}
