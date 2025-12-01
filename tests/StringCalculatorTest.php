<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class StringCalculatorTest extends TestCase
{
  private StringCalculator $calculator;

  protected function setUp(): void
  {
    $this->calculator = new StringCalculator();
  }

  public function test1_EmptyStringReturnsZero(): void
  {
    $this->assertEquals(0, $this->calculator->add(""));
  }

  public function test2_SingleNumber(): void
  {
    $this->assertEquals(5, $this->calculator->add("5"));
  }

  public function test3_TwoNumbersCommaSeparated(): void
  {
    $this->assertEquals(3, $this->calculator->add("1,2"));
  }

  public function test4_NewLineAsDelimiter(): void
  {
    $this->assertEquals(6, $this->calculator->add("1\n2,3"));
  }

  public function test5_SingleCustomDelimiter(): void
  {
    $this->assertEquals(6, $this->calculator->add("//;\n1;2;3"));
  }

  public function test6_SingleCustomDelimiterMixed(): void
  {
    $this->assertEquals(10, $this->calculator->add("//;\n1;2\n3,4"));
  }

  public function test7_LongSingleDelimiter(): void
  {
    $this->assertEquals(6, $this->calculator->add("//[***]\n1***2***3"));
  }

  public function test8_LongDelimiterWithText(): void
  {
    $this->assertEquals(60, $this->calculator->add("//[abc]\n10abc20abc30"));
  }

  public function test9_MultipleShortDelimiters(): void
  {
    $this->assertEquals(6, $this->calculator->add("//[*][%]\n1*2%3"));
  }

  public function test10_MultipleLongDelimiters(): void
  {
    $this->assertEquals(6, $this->calculator->add("//[***][%%]\n1***2%%3"));
  }

  public function test11_MultipleMixedLengthDelimiters(): void
  {
    $this->assertEquals(35, $this->calculator->add("//[abc][#]\n5abc10#20"));
  }

  public function test12_ThreeDelimitersOfDifferentLength(): void
  {
    $this->assertEquals(
      10,
      $this->calculator->add("//[*][%%][+++]\n1*2%%3+++4")
    );
  }

  public function test13_DelimiterWithRegexSpecialCharacters(): void
  {
    $this->assertEquals(6, $this->calculator->add("//[.*+?]\n1.*+?2.*+?3"));
  }

  public function test14_NumbersAbove1000Ignored(): void
  {
    $this->assertEquals(5, $this->calculator->add("2,1001,3"));
  }

  public function test15_LongDelimiterWithBigNumbers(): void
  {
    $this->assertEquals(1002, $this->calculator->add("//[***]\n1000***1001***2"));
  }

  public function test16_SingleNegativeThrowsException(): void
  {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage("Negatives not allowed: -1");

    $this->calculator->add("1,-1,2");
  }

  public function test17_MultipleNegativesThrowException(): void
  {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage("Negatives not allowed: -2, -4");

    $this->calculator->add("//[***]\n1***-2***3***-4");
  }

  public function test18_OnlyDelimitersNoNumbers(): void
  {
    $this->assertEquals(0, $this->calculator->add("//[***][%%]\n***%%***"));
  }
}
