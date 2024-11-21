<?php

class PaymentPayload extends CComponent
{
    protected $_model;

    function __construct($model)
    {
        $this->_model = $model;
    }

    public function getArray()
    {
        $payload = [
            'amount' => $this->_model->amount,
            'transaction_id' => time() . mt_rand(11111, 99999),
            'customer_fields' => $this->_model->customerFields,
            'bill_fields' => $this->_model->billFields,
            'account_number' => (int) $this->_model->accountData['accountNumber'],
            'account_type' => $this->_paymentAccountType($this->_model->accountData['accountType']),
            'additional_data_fields' => $this->_model->additionalDataFields,
        ];

        // Cuando un servicio tiene una consulta previa (retorna queries_debt=true)
        // se debe enviar los valores en customer_fields
        // y en customer_temporary_fields no se debe enviar ningún valor
        if (!$this->_model->service->queries_debt) {
            $payload['customer_temporary_fields'] = $this->_model->customerTemporaryFields;
        }

        $cbf = $this->_model->commissionBillFields;
        if ($cbf) {
            $payload['commission_bill_fields'] = $cbf;
        }

        if ($payload['account_type'] == 1) {
            $payload['masked_card_number'] = $this->_model->accountData['maskedCreditCardNumber'];
        }

        return $payload;
    }

    private function _paymentAccountType($at)
    {
        switch ($at) {
            case 'AH':
                return 2;
                break;

            case 'TJ':
                return 1;
                break;

            default:
                return 2;
                break;
        }
    }
}
