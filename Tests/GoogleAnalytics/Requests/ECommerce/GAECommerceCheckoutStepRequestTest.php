<?php

namespace GoogleAnalytics\Requests\ECommerce;

use Money\Currency;
use Money\Money;
use ReflectionClass;
use ReflectionException;
use ScAnalytics\Analytics;
use ScAnalytics\Core\Product;
use ScAnalytics\Core\Scope;
use ScAnalytics\GoogleAnalytics\GAParameter;
use ScAnalytics\GoogleAnalytics\Requests\ECommerce\GAECommerceCheckoutOptionRequest;
use ScAnalytics\GoogleAnalytics\Requests\ECommerce\GAECommerceCheckoutStepRequest;
use PHPUnit\Framework\TestCase;

/**
 * Tests the GAECommerceCheckoutStepRequest class.
 *
 * @author Jan-Nicklas Adler
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 * @copyright All Rights Reserved.
 */
class GAECommerceCheckoutStepRequestTest extends TestCase
{

    public function test__construct(): void
    {
        Analytics::init();
        $req = new GAECommerceCheckoutStepRequest(null, [new Product("id", new Money(300, new Currency("EUR")), new Money(250, new Currency("EUR")), "key", "name", "category", "variant", "brand", 2, "coupon")], 2, "PayPal");

        self::assertEquals("pageview", $req->getParameters()[GAParameter::$TYPE->getName()]);
        self::assertEquals("checkout", $req->getParameters()[GAParameter::$PRODUCTACTION->getName()]);
        self::assertEquals("USD", $req->getParameters()[GAParameter::$CURRENCY->getName()]);
        self::assertEquals(2, $req->getParameters()[GAParameter::$CHECKOUTSTEP->getName()]);
        self::assertEquals("PayPal", $req->getParameters()[GAParameter::$CHECKOUTSTEPOPTION->getName()]);


        self::assertEquals("id", $req->getParameters()[GAParameter::$PRODUCTSKU->withValue(1)->getName()]);
        self::assertEquals("key", $req->getParameters()[GAParameter::$PRODUCTNAME->withValue(1)->getName()]);
        self::assertEquals("brand", $req->getParameters()[GAParameter::$PRODUCTBRAND->withValue(1)->getName()]);
        self::assertEquals("category", $req->getParameters()[GAParameter::$PRODUCTCATEGORY->withValue(1)->getName()]);
        self::assertEquals("variant", $req->getParameters()[GAParameter::$PRODUCTVARIANT->withValue(1)->getName()]);
        self::assertEquals(1, $req->getParameters()[GAParameter::$PRODUCTPOSITION->withValue(1)->getName()]);
        self::assertEquals("3.00", $req->getParameters()[GAParameter::$PRODUCTPRICE->withValue(1)->getName()]);
        self::assertEquals(2, $req->getParameters()[GAParameter::$PRODUCTQUANTITY->withValue(1)->getName()]);
        self::assertEquals("coupon", $req->getParameters()[GAParameter::$PRODUCTCOUPON->withValue(1)->getName()]);
    }

    /**
     * @throws ReflectionException
     */
    protected function tearDown(): void
    {
        self::set("analytics", null);
        self::set("analyticsList", []);
        self::set("scope", new Scope());
    }

    /**
     * Helper function setting properties using reflection.
     *
     * @param string $field Name of the field
     * @param mixed $value Value to set
     * @throws ReflectionException
     */
    private static function set(string $field, $value): void
    {
        $apiDataClass = new ReflectionClass(Analytics::class);
        $prop = $apiDataClass->getProperty($field);
        $prop->setAccessible(true);
        $prop->setValue($value);
    }
}
