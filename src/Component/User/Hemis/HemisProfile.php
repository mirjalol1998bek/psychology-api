<?php

declare(strict_types=1);

namespace App\Component\User\Hemis;

final readonly class HemisProfile
{
    public function __construct(
        public string $hemisId,
        public string $login,
        public string $fullName,
        public ?string $email,
        public string $type,
        public ?string $picture,
        public ?string $phone,
        public ?string $groupName,
        public ?string $facultyName,
        /**
         * Xodim ID'lari (`employee_id_number`) — HEMIS sinxroni xodimni shu bilan
         * saqlaydi; xodimning OAuth `login`i esa foydalanuvchi nomi (masalan
         * `bekzod_utekov`), unga teng emas.
         *
         * @var list<string>
         */
        public array $employeeIds = [],
        /**
         * Kelgan maydon nomlari (qiymatlarsiz) — mos yozuv topilmaganda logga.
         *
         * @var list<string>
         */
        public array $fieldNames = [],
    ) {
    }

    public function isEmployee(): bool
    {
        return $this->type === 'employee' || $this->type === 'teacher' || $this->type === 'staff';
    }

    public function resolveEmail(): string
    {
        if ($this->email !== null && $this->email !== '') {
            return $this->email;
        }

        return $this->login . '@hemis.uzswlu.uz';
    }
}
