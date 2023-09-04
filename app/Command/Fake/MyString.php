<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Command\Fake;

use App\Util\MyString as MyStringNew;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;

#[Command]
class MyString extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('fake:my-string');
    }

    public function handle(): void
    {
        //        $common = MyStringNew::findCommonSubstring('abcdefg', 'defghij');
        //        if (! empty($common)) {
        //            $this->info("重复部分: " . $common);
        //        } else {
        //            $this->info('没有重复部分');
        //        }
        //
        //        $str1 = "apple123";
        //        $str2 = "treeapple";
        //
        //        $commonPrefix = MyStringNew::findCommonPrefix($str1, $str2);
        //        if (! empty($commonPrefix)) {
        //            $this->info("重复开头部分: " . $commonPrefix);
        //        } else {
        //            $this->info('没有重复开头部分');
        //        }

        //        $data = [
        //            'TA-LXL-GrayOrg-L',
        //            'TA-LXL-LightGreen-L',
        //            'TA-LXL-Leopard-L',
        //            'TA-LXL-WhiteBlack-L',
        //            'TA-LXL-Black-L',
        //            'SWY-SW-BLACK-US',
        //            'SWY-SW-Apricot-US',
        //            'SWY-SW-Grey-US',
        //            'SWB880-black',
        //            'SWB880-khaki',
        //            'SWB880-blue',
        //            'SWB880-white',
        //            'SWB880-grey',
        //            'L-SWY-707-US-GRAY',
        //            'L-SWY-707-US-NAVY BLUE',
        //            'L-SWY-707-US-RED',
        //            'L-SWY-707-US-PINK',
        //            'L-SWY-707-US-BLACK',
        //            'CB-8265-black',
        //            'ZM-6672-black',
        //            'CB-10001-15-grey',
        //            'CB-10001-15-black',
        //            'CB-10001-15-Navy Blue',
        //            'CB-10001-17-black',
        //            'CB-10001-17-Navy Blue',
        //            'CB-10001-17-grey',
        //            'LK-20123-Black-US',
        //            'LK-20123-Silver-US',
        //            'LK-20123-White-US',
        //            'LK-20123-Pink-US',
        //            'LK-20123-Khaki-US',
        //            'TBG-13047-Coffee-US',
        //            'TBG-13047-Pink-US',
        //            'TBG-13047-Black-US',
        //            'TBG-13047-Red-US',
        //            'TBG-13047-Grey-US',
        //            'HZ-11595-KHAKI-US',
        //            'HZ-11595-WHITE-US',
        //            'HZ-11595-BROWN-US',
        //            'HZ-11595-MUD-US',
        //            'HZ-11595-BLACK-US',
        //            'UW-6619-BEIGE-US',
        //            'UW-6619-BLACK-US',
        //            'UW-6619-BROWN-US',
        //            'UW-6619-COFFEE-new-US',
        //            'UW-6619-COFFEE-US',
        //            'UW-6619-WHITE-US',
        //            'FW-LW259-BEIGE-US',
        //            'FW-LW259-BLACK-US',
        //            'FW-LW259-BLUE-US',
        //            'FW-LW259-PURPLE-US',
        //            'FW-LW259-WHITE-US',
        //            'FW-LW259-GREEN-US',
        //            'FW-L-LW259-BEIGE-US',
        //            'FW-L-LW259-BLACK-US',
        //            'FW-L-LW259-BLUE-US',
        //            'FW-L-LW259-PURPLE-US',
        //            'FW-L-LW259-WHITE-US',
        //            'FW-L-LW259-GREEN-US',
        //            'ZK-2054-BLACK-US',
        //            'ZK-2054-BEIGE-US',
        //            'ZK-2054-WHITE-new-US',
        //            'ZK-2054-KHAKI-US',
        //            'ZK-2054-BROWN-US',
        //            'DFMP-SHIN284-BLACK-US',
        //            'DFMP-SHIN284-BLUE-US',
        //            'DFMP-SHIN284-GREEN-US',
        //            'DFMP-SHIN284-KHAKI-US',
        //            'DFMP-SHIN284-PINK-US',
        //            'DFMP-SHIN284-WHITE-US',
        //            'DFMP-STR304-BEIGE-US',
        //            'DFMP-STR304-BLACK-US',
        //            'DFMP-STR304-GRADIENT-US',
        //            'DFMP-STR304-GREEN-US',
        //            'DFMP-STR304-PINK-US',
        //            'DFMP-STR304-WHITE-US',
        //            'SY-3344-BLACK-US',
        //            'SY-3344-GOLD-US',
        //            'SY-3344-SILVER-US',
        //            'SY-3347-BLACK-US',
        //            'SY-3347-RED-US',
        //            'SY-3347-WINEREDUS',
        //            'JS-0007-BLACK-US',
        //            'JS-0007-BLACKSILVER-US',
        //            'JS-0007-GOLD-US',
        //            'JS-0007-GREY-US',
        //            'JS-0007-SILVER-US',
        //            'KL-20651-92-BLACK-US',
        //            'KL-20651-92-GOLD-US',
        //            'KL-20651-92-SILVER-US',
        //            'QY-20651-E7-BLACK-US',
        //            'QY-20651-E7-GOLD-US',
        //            'QY-20651-E7-SILVER-US',
        //            'HCY-A3518-BLACK-US',
        //            'HCY-A3518-BROWN-US',
        //            'HCY-A3518-COFFEE-US',
        //            'HCY-A3518-GREEN-US',
        //            'HCY-A3518-PINK-US',
        //            'HCY-A3518-PURPLE-US',
        //            'HCY-A3518-WHITE-US',
        //            'HCY-A3518-YELLOW-US',
        //            'HCY-A3518-NUDE-US',
        //            'SN-6824-BEIGE-US',
        //            'SN-6824-BLACK-US',
        //            'SN-6824-BROWN-US',
        //            'SN-6824-WHITE-US',
        //            'SM-009-WHITE-US',
        //            'SM-009-KHAKI-US',
        //            'SM-009-BLACK-US',
        //            'SM-009-GREY-US',
        //            'SM-009-COFFEE-US',
        //            'SM-009-GREEN-US',
        //            'SM-009-BEIGE-US',
        //            'SM-009-BROWN-US',
        //            'HZ-828-BLACK-US',
        //            'HZ-828-BLACK-new-US',
        //            'HZ-828-BROWN-US',
        //            'HZ-828-COFFEE-US',
        //            'HZ-828-WHITE-US',
        //            'HZ-828-YELLOW-US',
        //            'HZ-828-PURPLE-US',
        //            'HZ-828-GREEN-US',
        //            'HZ-828-SILVER-US',
        //            'HZ-828-PINK-US',
        //            'HJ-9389-BLACK-US',
        //            'HJ-9389-GOLD-US',
        //            'HJ-9389-GREEN-US',
        //            'HJ-9389-GUNSILVER-US',
        //            'HJ-9389-KHAKI-US',
        //            'HJ-9389-OLIVE-US',
        //            'HJ-9389-ORANGE-US',
        //            'HJ-9389-PINK-US',
        //            'HJ-9389-SILVER-US',
        //            'HJ-9389-WHITE-US',
        //            'HZ-60935-BLACK-US',
        //            'HZ-60935-BROWN-US',
        //            'HZ-60935-GREEN-US',
        //            'HZ-60935-WHITE-US',
        //            'AP-1925-BLACK-US',
        //            'AP-1925-SILVER-US',
        //            'AP-1925-WHITE-US',
        //            'HX-7757-BLACK-US',
        //            'HX-7757-BROWN-US',
        //            'HX-7757-KHAKI-US',
        //            'HX-7757-WHITE-US',
        //            'SWY-B1683-APRICOT-US',
        //            'SWY-B1683-BLACK-US',
        //            'SWY-B1683-KHAKI-US',
        //            'SWY-B1683-SILVER-US',
        //            'SWY-B1683-WHITE-US',
        //            'SWY-B249-APRICOT-US',
        //            'SWY-B249-BLACK-US',
        //            'SWY-B249-BROWN-US',
        //            'SWY-B249-GUNSILVER-US',
        //            'SWY-B249-WHITE-US',
        //        ];
        $data = [
            'TA-LXL-GrayOrg-L' => 'TA-LXL',
            'TA-LXL-LightGreen-L' => 'TA-LXL',
            'TA-LXL-Leopard-L' => 'TA-LXL',
            'TA-LXL-WhiteBlack-L' => 'TA-LXL',
            'TA-LXL-Black-L' => 'TA-LXL',
            'SWY-SW-BLACK-US' => 'SWY-SW-US',
            'SWY-SW-Apricot-US' => 'SWY-SW-US',
            'SWY-SW-Grey-US' => 'SWY-SW-US',
            'SWB880-black' => 'SWB880',
            'SWB880-khaki' => 'SWB880',
            'SWB880-blue' => 'SWB880',
            'SWB880-white' => 'SWB880',
            'SWB880-grey' => 'SWB880',
            'L-SWY-707-US-GRAY' => 'L-SWY-707-US',
            'L-SWY-707-US-NAVY BLUE' => 'L-SWY-707-US',
            'L-SWY-707-US-RED' => 'L-SWY-707-US',
            'L-SWY-707-US-PINK' => 'L-SWY-707-US',
            'L-SWY-707-US-BLACK' => 'L-SWY-707-US',
            'CB-8265-black' => 'CB-8265',
            'ZM-6672-black' => 'ZM-6672',
            'CB-10001-15-grey' => 'CB-10001',
            'CB-10001-15-black' => 'CB-10001',
            'CB-10001-15-Navy Blue' => 'CB-10001',
            'CB-10001-17-black' => 'CB-10001',
            'CB-10001-17-Navy Blue' => 'CB-10001',
            'CB-10001-17-grey' => 'CB-10001',
            'LK-20123-Black-US' => 'LK-20123',
            'LK-20123-Silver-US' => 'LK-20123',
            'LK-20123-White-US' => 'LK-20123',
            'LK-20123-Pink-US' => 'LK-20123',
            'LK-20123-Khaki-US' => 'LK-20123',
            'TBG-13047-Coffee-US' => 'TBG-13047',
            'TBG-13047-Pink-US' => 'TBG-13047',
            'TBG-13047-Black-US' => 'TBG-13047',
            'TBG-13047-Red-US' => 'TBG-13047',
            'TBG-13047-Grey-US' => 'TBG-13047',
            'HZ-11595-KHAKI-US' => 'HZ-11595',
            'HZ-11595-WHITE-US' => 'HZ-11595',
            'HZ-11595-BROWN-US' => 'HZ-11595',
            'HZ-11595-MUD-US' => 'HZ-11595',
            'HZ-11595-BLACK-US' => 'HZ-11595',
            'UW-6619-BEIGE-US' => 'UW-6619',
            'UW-6619-BLACK-US' => 'UW-6619',
            'UW-6619-BROWN-US' => 'UW-6619',
            'UW-6619-COFFEE-new-US' => 'UW-6619',
            'UW-6619-COFFEE-US' => 'UW-6619',
            'UW-6619-WHITE-US' => 'UW-6619',
            'FW-LW259-BEIGE-US' => 'FW-LW259',
            'FW-LW259-BLACK-US' => 'FW-LW259',
            'FW-LW259-BLUE-US' => 'FW-LW259',
            'FW-LW259-PURPLE-US' => 'FW-LW259',
            'FW-LW259-WHITE-US' => 'FW-LW259',
            'FW-LW259-GREEN-US' => 'FW-L-LW259',
            'FW-L-LW259-BEIGE-US' => 'FW-LW259',
            'FW-L-LW259-BLACK-US' => 'FW-LW259',
            'FW-L-LW259-BLUE-US' => 'FW-LW259',
            'FW-L-LW259-PURPLE-US' => 'FW-LW259',
            'FW-L-LW259-WHITE-US' => 'FW-LW259',
            'FW-L-LW259-GREEN-US' => 'FW-L-LW259',
            'ZK-2054-BLACK-US' => 'ZK-2054',
            'ZK-2054-BEIGE-US' => 'ZK-2054',
            'ZK-2054-WHITE-new-US' => 'ZK-2054',
            'ZK-2054-KHAKI-US' => 'ZK-2054',
            'ZK-2054-BROWN-US' => 'ZK-2054',
            'DFMP-SHIN284-BLACK-US' => 'DFMP-SHIN284',
            'DFMP-SHIN284-BLUE-US' => 'DFMP-SHIN284',
            'DFMP-SHIN284-GREEN-US' => 'DFMP-SHIN284',
            'DFMP-SHIN284-KHAKI-US' => 'DFMP-SHIN284',
            'DFMP-SHIN284-PINK-US' => 'DFMP-SHIN284',
            'DFMP-SHIN284-WHITE-US' => 'DFMP-SHIN284',
            'DFMP-STR304-BEIGE-US' => 'DFMP-STR304',
            'DFMP-STR304-BLACK-US' => 'DFMP-STR304',
            'DFMP-STR304-GRADIENT-US' => 'DFMP-STR304',
            'DFMP-STR304-GREEN-US' => 'DFMP-STR304',
            'DFMP-STR304-PINK-US' => 'DFMP-STR304',
            'DFMP-STR304-WHITE-US' => 'DFMP-STR304',
            'SY-3344-BLACK-US' => 'SY-3344',
            'SY-3344-GOLD-US' => 'SY-3344',
            'SY-3344-SILVER-US' => 'SY-3344',
            'SY-3347-BLACK-US' => 'SY-3347',
            'SY-3347-RED-US' => 'SY-3347',
            'SY-3347-WINEREDUS' => 'SY-3347',
            'JS-0007-BLACK-US' => 'JS-0007',
            'JS-0007-BLACKSILVER-US' => 'JS-0007',
            'JS-0007-GOLD-US' => 'JS-0007',
            'JS-0007-GREY-US' => 'JS-0007',
            'JS-0007-SILVER-US' => 'JS-0007',
            'KL-20651-92-BLACK-US' => 'KL-20651-92',
            'KL-20651-92-GOLD-US' => 'KL-20651-92',
            'KL-20651-92-SILVER-US' => 'KL-20651-92',
            'QY-20651-E7-BLACK-US' => 'QY-20651-E7',
            'QY-20651-E7-GOLD-US' => 'QY-20651-E7',
            'QY-20651-E7-SILVER-US' => 'QY-20651-E7',
            'HCY-A3518-BLACK-US' => 'HCY-A3518',
            'HCY-A3518-BROWN-US' => 'HCY-A3518',
            'HCY-A3518-COFFEE-US' => 'HCY-A3518',
            'HCY-A3518-GREEN-US' => 'HCY-A3518',
            'HCY-A3518-PINK-US' => 'HCY-A3518',
            'HCY-A3518-PURPLE-US' => 'HCY-A3518',
            'HCY-A3518-WHITE-US' => 'HCY-A3518',
            'HCY-A3518-YELLOW-US' => 'HCY-A3518',
            'HCY-A3518-NUDE-US' => 'HCY-A3518',
            'SN-6824-BEIGE-US' => 'SN-6824',
            'SN-6824-BLACK-US' => 'SN-6824',
            'SN-6824-BROWN-US' => 'SN-6824',
            'SN-6824-WHITE-US' => 'SN-6824',
            'SM-009-WHITE-US' => 'SM-009',
            'SM-009-KHAKI-US' => 'SM-009',
            'SM-009-BLACK-US' => 'SM-009',
            'SM-009-GREY-US' => 'SM-009',
            'SM-009-COFFEE-US' => 'SM-009',
            'SM-009-GREEN-US' => 'SM-009',
            'SM-009-BEIGE-US' => 'SM-009',
            'SM-009-BROWN-US' => 'SM-009',
            'HZ-828-BLACK-US' => 'HZ-828',
            'HZ-828-BLACK-new-US' => 'HZ-828',
            'HZ-828-BROWN-US' => 'HZ-828',
            'HZ-828-COFFEE-US' => 'HZ-828',
            'HZ-828-WHITE-US' => 'HZ-828',
            'HZ-828-YELLOW-US' => 'HZ-828',
            'HZ-828-PURPLE-US' => 'HZ-828',
            'HZ-828-GREEN-US' => 'HZ-828',
            'HZ-828-SILVER-US' => 'HZ-828',
            'HZ-828-PINK-US' => 'HZ-828',
            'HJ-9389-BLACK-US' => 'HJ-9389',
            'HJ-9389-GOLD-US' => 'HJ-9389',
            'HJ-9389-GREEN-US' => 'HJ-9389',
            'HJ-9389-GUNSILVER-US' => 'HJ-9389',
            'HJ-9389-KHAKI-US' => 'HJ-9389',
            'HJ-9389-OLIVE-US' => 'HJ-9389',
            'HJ-9389-ORANGE-US' => 'HJ-9389',
            'HJ-9389-PINK-US' => 'HJ-9389',
            'HJ-9389-SILVER-US' => 'HJ-9389',
            'HJ-9389-WHITE-US' => 'HJ-9389',
            'HZ-60935-BLACK-US' => 'HZ-60935',
            'HZ-60935-BROWN-US' => 'HZ-60935',
            'HZ-60935-GREEN-US' => 'HZ-60935',
            'HZ-60935-WHITE-US' => 'HZ-60935',
            'AP-1925-BLACK-US' => 'AP-1925',
            'AP-1925-SILVER-US' => 'AP-1925',
            'AP-1925-WHITE-US' => 'AP-1925',
            'HX-7757-BLACK-US' => 'HX-7757',
            'HX-7757-BROWN-US' => 'HX-7757',
            'HX-7757-KHAKI-US' => 'HX-7757',
            'HX-7757-WHITE-US' => 'HX-7757',
            'SWY-B1683-APRICOT-US' => 'SWY-B1683',
            'SWY-B1683-BLACK-US' => 'SWY-B1683',
            'SWY-B1683-KHAKI-US' => 'SWY-B1683',
            'SWY-B1683-SILVER-US' => 'SWY-B1683',
            'SWY-B1683-WHITE-US' => 'SWY-B1683',
            'SWY-B249-APRICOT-US' => 'SWY-B249',
            'SWY-B249-BLACK-US' => 'SWY-B249',
            'SWY-B249-BROWN-US' => 'SWY-B249',
            'SWY-B249-GUNSILVER-US' => 'SWY-B249',
            'SWY-B249-WHITE-US' => 'SWY-B249',
            'DG-023-Brown-US' => 'DG-023',
            'DG-023-Grey-US' => 'DG-023',
            'DG-023-Black-US' => 'DG-023',
            'DG-023-Black&Brown-US' => 'DG-023',
            'DG-023-White&Brown-US' => 'DG-023',
            'DG-023-Coffee-US' => 'DG-023',
            'DG-023-Light Brown-US' => 'DG-023',
            'CQ-B230701-Coffee-US' => 'CQ-B230701',
            'CQ-B230701-Grey-US' => 'CQ-B230701',
            'CQ-B230701-White with Brown-US' => 'CQ-B230701',
            'CQ-B230701-Black-US' => 'CQ-B230701',
            'CQ-B230701-Brown-US' => 'CQ-B230701',
            'ASN-5024-1-BLACK' => 'ASN-5024-1',
            'ASN-5024-1-BROWN' => 'ASN-5024-1',
            'ASN-5024-1-WHITE' => 'ASN-5024-1',
            'ASN-5024-1-YELLOW' => 'ASN-5024-1',
            'ASN-5024-BLACK-US' => 'ASN-5024',
            'ASN-5024-BROWN-US' => 'ASN-5024',
            'DEY-6A20615-BROWN-US' => 'DEY-6A20615',
            'DEY-6A20615-WHITE-US' => 'DEY-6A20615',
            'DFMP-FL275-BLACK-US' => 'DFMP-FL275',
            'DFMP-FL275-BLUE-US' => 'DFMP-FL275',
            'DFMP-FL275-GREEN-US' => 'DFMP-FL275',
            'DFMP-FL275-KHAKI-US' => 'DFMP-FL275',
            'DFMP-FL275-PINK-US' => 'DFMP-FL275',
            'DFMP-FL275-WHITE-US' => 'DFMP-FL275',
            'DW-DM810-BLACK-US' => 'DW-DM810',
            'DW-DM810-KHAKI-US' => 'DW-DM810',
            'FW-LW186-BLACK-US' => 'FW-LW186',
            'FW-LW186-BROWN-US' => 'FW-LW186',
            'FW-LW259-YELLOW-US' => 'FW-LW259',
            'GF-6617-BLACK-US' => 'GF-6617',
            'GF-6617-BROWN-US' => 'GF-6617',
            'GF-6617-KHAKI-US' => 'GF-6617',
            'HL-P114-BLACK-US' => 'HL-P114',
            'HL-P114-BROWN-US' => 'HL-P114',
            'HL-P114-WHITE-US' => 'HL-P114',
            'HL-P114-YELLOW-US' => 'HL-P114',
            'JMT-LL-20-black' => 'JMT-LL',
            'JMT-LL-20-blue' => 'JMT-LL',
            'JMT-LL-20-grey' => 'JMT-LL',
            'JMT-LL-20-sliver' => 'JMT-LL',
            'JMT-LL-28-grey' => 'JMT-LL',
            'JMT-LL-28-sliver' => 'JMT-LL',
            'LW-323-BLACK-US' => 'LW-323',
            'LW-323-BROWN-US' => 'LW-323',
            'LW-323-WHITE-US' => 'LW-323',
            'LW147-BLACK-US' => 'LW147',
            'LW147-BLUE-US' => 'LW147',
            'LW147-YELLOW-US' => 'LW147',
            'LW213-BLACK-US' => 'LW213',
            'LW213-BLUE-US' => 'LW213',
            'LW213-BROWN-US' => 'LW213',
            'LW213-GREEN-new-US' => 'LW213',
            'LW213-GREEN-US' => 'LW213',
            'LW213-GREEN2-US' => 'LW213',
            'LW213-KHAKI-US' => 'LW213',
            'LW213-WHITE-US' => 'LW213',
            'LW284-BLACK-US' => 'LW284',
            'LW284-WHITE-US' => 'LW284',
            'SC-88015-BLACK-US' => 'SC-88015',
            'SC-88015-GREEN-US' => 'SC-88015',
            'SC-88015-PINK-US' => 'SC-88015',
            'SC-88015-RED-US' => 'SC-88015',
            'SMCD-8994-PURPLE-US' => 'SMCD-8994',
            'SMCD-8994-WHITE-US' => 'SMCD-8994',
            'SMCD-8994-YELLOW-US' => 'SMCD-8994',
            'SS-220724-PINK-US' => 'SS-220725',
            'SS-220724-WHITE-US' => 'SS-220725',
            'SS-220724-YELLOW-US' => 'SS-220725',
            'SS-220725-BEIGE-US' => 'SS-220725',
            'SS-220725-BLACK-US' => 'SS-220725',
            'SS-220725-BROWN-US' => 'SS-220725',
            'SS-220725-PINK-US' => 'SS-220725',
            'SS-220725-PURPLE-US' => 'SS-220725',
            'SYD-8076-BLACK-US' => 'SYD-8076',
            'SYD-8076-BROWN-US' => 'SYD-8076',
            'SYD-8076-GREEN-US' => 'SYD-8076',
            'WT-S1821-BLACK-US' => 'WT-S1821',
            'WT-S1821-BROWN-US' => 'WT-S1821',
            'WT-S1821-KHAKI-US' => 'WT-S1821',
            'WT-S1821-WHITE-US' => 'WT-S1821',
            'YQK-T6074-GREEN-US' => 'YQK-T6074',
            'YQK-T6074-KHAKI-US' => 'YQK-T6074',
            'YQK-T6074-RED-US' => 'YQK-T6074',
            'ZK-2054-WHITE-US' => 'ZK-2054',
            'ZK-K2063A-BLACK-US' => 'ZK-K2063A',
            'ZK-K2063A-BROWN-US' => 'ZK-K2063A',
            'ZK-K2063A-MUD-US' => 'ZK-K2063A',
            'ZK-K2063A-WHITE-US' => 'ZK-K2063A',
        ];
        foreach ($data as $key => $datum) {
            $skuToSpu = $this->skuToSpu($key);
            $sellerSku2Spu = $this->sellerSku2Spu($key);

            if ($datum !== $skuToSpu || $datum !== $sellerSku2Spu) {
                $win = '';
                if ($datum === $skuToSpu) {
                    $win = '[skuToSpu win]';
                } elseif ($datum === $sellerSku2Spu) {
                    $win = '[sellerSku2Spu win]';
                }
                $this->info(sprintf('%s 期望:%s VS skuToSpu:%s VS sellerSku2Spu:%s  Result:%s', $key, $datum, $skuToSpu, $sellerSku2Spu, $win));
            }
        }
    }

    public function skuToSpu($seller_sku)
    {
        $sku = strtoupper($seller_sku);
        $skuArr = explode('-', $sku);
        $skuArr = array_diff($skuArr, ['NEW', 'US', 'EU']);
        $skuArr = array_filter($skuArr);
        $pop = array_pop($skuArr);
        if ($pop === 'L') { // 把L当到最后的要额外处理
            array_pop($skuArr);
            $skuArr[] = 'L';
        }
        return implode('-', $skuArr);
    }

    public function sellerSku2Spu(string $seller_sku): string
    {
        $count = substr_count($seller_sku, '-');
        if ($count > 4) {
            return '';
        }
        if ($count > 2) {
            --$count;
        }
        $seller_sku = str_replace(['BLACK', 'BLUE', 'YELLOW', 'BROWN', 'GREEN', 'KHAKI', 'WHITE', 'ORANGE', 'PINK', 'SILVER', 'GOLD', 'COFFEE'], '', $seller_sku);
        $pos = 0;
        for ($i = 0; $i < $count; ++$i) {
            $pos = strpos($seller_sku, '-', $pos + 1);
            if ($pos === false) {
                break;
            }
        }
        $substr = substr($seller_sku, 0, $pos);
        return $substr === false ? '' : trim($substr, '-');
    }
}
