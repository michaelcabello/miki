<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Journal extends Model
{
    protected $fillable = [
        'name',
        'code',
        'state',
        'use_documents',
        'active',
        'journal_type_id',
        'currency_id',
        'account_id',
        'default_debit_account_id',
        'default_credit_account_id',
        'suspense_account_id',
        'exchange_gain_account_id',
        'exchange_loss_account_id',
        'bank_name',
        'bank_account_number',
        'cci',
        'swift',
        'iban',
        'use_document_sequence',
        'document_prefix',
        'document_next_number',
        'allow_manual_entries',
        'settings',
    ];

    // Conversión automática de tipos
    protected $casts = [
        'state'                  => 'boolean',
        'active'                 => 'boolean',
        'use_documents'          => 'boolean',
        'use_document_sequence'  => 'boolean',
        'allow_manual_entries'   => 'boolean',
        'document_next_number'   => 'integer',
        'settings'               => 'array',
    ];

    // ─── Relaciones ───────────────────────────────────────────────

    /** Tipo de diario (Ventas, Compras, Banco, Caja, etc.) */
    public function journalType(): BelongsTo
    {
        return $this->belongsTo(JournalType::class);
    }

    /** Moneda opcional */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /** Cuenta contable principal de contrapartida */
    public function account(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Account::class, 'account_id');
    }

    /** Cuenta de débito por defecto (banco/caja) */
    public function defaultDebitAccount(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Account::class, 'default_debit_account_id');
    }

    /** Cuenta de crédito por defecto (banco/caja) */
    public function defaultCreditAccount(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Account::class, 'default_credit_account_id');
    }

    /** Cuenta puente / suspense para conciliación */
    public function suspenseAccount(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Account::class, 'suspense_account_id');
    }

    /** Cuenta de ganancia por diferencia de tipo de cambio */
    public function exchangeGainAccount(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Account::class, 'exchange_gain_account_id');
    }

    /** Cuenta de pérdida por diferencia de tipo de cambio */
    public function exchangeLossAccount(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Account::class, 'exchange_loss_account_id');
    }
}
