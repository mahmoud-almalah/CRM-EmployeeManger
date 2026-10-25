<?php

use Domain\User\ValueObjects\EmailObject;
use Domain\User\ValueObjects\NameObject;
use Domain\User\ValueObjects\PhoneObject;

describe('EmailObject', function () {
    it('normalizes case and whitespace', function () {
        expect((string) new EmailObject('  Mahmoud@Example.COM '))->toBe('mahmoud@example.com');
    });

    it('rejects an invalid address', function () {
        new EmailObject('not-an-email');
    })->throws(InvalidArgumentException::class);

    it('compares by value', function () {
        expect((new EmailObject('a@b.co'))->equals(new EmailObject('A@B.co')))->toBeTrue();
    });
});

describe('NameObject', function () {
    it('builds the full name and skips empty parts', function () {
        expect((new NameObject('Mahmoud', '', 'Almalah'))->fullName())->toBe('Mahmoud Almalah');
    });

    it('needs a first name', function () {
        new NameObject('  ');
    })->throws(InvalidArgumentException::class);

    it('round trips through an array', function () {
        $name = new NameObject('Mahmoud', 'A', 'Almalah');

        expect(NameObject::fromArray($name->toArray())->equals($name))->toBeTrue();
    });
});

describe('PhoneObject', function () {
    it('normalizes the country code and strips formatting', function () {
        $phone = new PhoneObject('(101) 234-5678', '0020');

        expect($phone->getCountryCode())->toBe('+0020')
            ->and($phone->getNumber())->toBe('1012345678');
    });

    it('round trips through its stored form', function () {
        $phone = new PhoneObject('1012345678', '+20');

        expect((string) $phone)->toBe('+20 1012345678')
            ->and(PhoneObject::fromStorage((string) $phone)->equals($phone))->toBeTrue();
    });

    it('builds a WhatsApp link without the plus sign', function () {
        expect((new PhoneObject('1012345678', '+20'))->getWhatsAppLink())->toBe('https://wa.me/201012345678');
    });

    it('rejects an empty number', function () {
        new PhoneObject('abc', '+20');
    })->throws(InvalidArgumentException::class);
});
