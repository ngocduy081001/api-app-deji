<?php

namespace Vendor\Warranty\Http\Livewire;

use Livewire\Component;
use Vendor\Warranty\Models\Warranty;
use Vendor\Customer\Models\Customer;
use Vendor\Warranty\Http\Requests\UpdateWarrantyRequest;
use Illuminate\Validation\ValidationException;

class EditWarranty extends Component
{
    public Warranty $warranty;

    public $month;
    public $active_date;
    public $time_expired;
    public $status;

    // Customer info properties
    public $customer_name;
    public $customer_email;
    public $customer_phone;

    // Counter to force re-render
    public $resetCounter = 0;

    public function mount(Warranty $warranty)
    {
        $this->warranty = $warranty->load('customer', 'product');

        // Set initial values
        $this->month = $warranty->month;
        $this->active_date = $warranty->active_date ? $warranty->active_date->format('Y-m-d') : '';
        $this->time_expired = $warranty->time_expired ? $warranty->time_expired->format('Y-m-d') : '';
        $this->status = $warranty->status;

        // Set customer info
        $this->customer_name = $warranty->customer?->name ?? '';
        $this->customer_email = $warranty->customer?->email ?? '';
        $this->customer_phone = $warranty->customer?->phone ?? '';
    }

    public function rules()
    {
        return [
            'month' => ['nullable', 'integer', 'min:1', 'max:120'],
            'active_date' => ['nullable', 'date'],
            'time_expired' => ['nullable', 'date', 'after:active_date'],
            'status' => ['nullable', 'string', 'in:clear,active,expired'],
        ];
    }

    public function updated($propertyName)
    {
        // Auto-calculate expiration date when active_date or month changes
        if (in_array($propertyName, ['active_date', 'month'])) {
            $this->calculateExpirationDate();
        }

        $this->validateOnly($propertyName);
    }

    protected function calculateExpirationDate()
    {
        if (!empty($this->active_date) && !empty($this->month)) {
            $activeDate = new \DateTime($this->active_date);
            $activeDate->modify("+{$this->month} months");
            $this->time_expired = $activeDate->format('Y-m-d');
        } elseif (!empty($this->active_date) && $this->warranty->month) {
            $activeDate = new \DateTime($this->active_date);
            $activeDate->modify("+{$this->warranty->month} months");
            $this->time_expired = $activeDate->format('Y-m-d');
        } elseif (!empty($this->month) && $this->warranty->active_date) {
            $activeDate = new \DateTime($this->warranty->active_date->format('Y-m-d'));
            $activeDate->modify("+{$this->month} months");
            $this->time_expired = $activeDate->format('Y-m-d');
        }
    }

    public function resetForm()
    {
        // Reload warranty from database to get fresh values
        $this->warranty->refresh();
        $this->warranty->load('customer', 'product');

        // Reset warranty fields to database values
        $this->month = 12;
        $this->active_date =  '';
        $this->time_expired =  '';
        $this->status = 'clear';

        $this->customer_name = '';
        $this->customer_email = '';
        $this->customer_phone = '';

        $this->resetValidation();

        // Increment counter to force re-render
        $this->resetCounter++;
    }

    public function update()
    {
        $this->validate();

        $data = [
            'month' => $this->month,
            'active_date' => $this->active_date ? $this->active_date . ' 00:00:00' : null,
            'time_expired' => $this->time_expired ? $this->time_expired . ' 00:00:00' : null,
            'status' => $this->status,
        ];

        // Calculate expiration date if needed
        if (!empty($data['active_date']) && !empty($data['month'])) {
            $activeDate = new \DateTime($data['active_date']);
            $activeDate->modify("+{$data['month']} months");
            $data['time_expired'] = $activeDate->format('Y-m-d H:i:s');
        } elseif (!empty($data['active_date']) && $this->warranty->month) {
            $activeDate = new \DateTime($data['active_date']);
            $activeDate->modify("+{$this->warranty->month} months");
            $data['time_expired'] = $activeDate->format('Y-m-d H:i:s');
        } elseif (!empty($data['month']) && $this->warranty->active_date) {
            $activeDate = new \DateTime($this->warranty->active_date);
            $activeDate->modify("+{$data['month']} months");
            $data['time_expired'] = $activeDate->format('Y-m-d H:i:s');
        }

        if ($this->customer_email) {
            $customer = Customer::where('email', $this->customer_email)
                ->first();
            if (!$customer) {
                $customer = Customer::create([
                    'name' => $this->customer_name ?? '',
                    'email' => $this->customer_email,
                    'phone' => $this->customer_phone ?? '',
                ]);
            }
            $data['customer_id'] = $customer->id;
        } else {
            $data['customer_id'] = null;
        }

        $this->warranty->update($data);


        session()->flash('success', 'Bảo hành đã được cập nhật thành công.');

        return redirect()->route('admin.warranties.show', $this->warranty->product);
    }

    public function render()
    {
        return view('warranty::livewire.edit-warranty');
    }
}
