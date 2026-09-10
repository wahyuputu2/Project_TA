<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ShippingService
{
    private string $apiKey;
    private string $baseUrl;

    // Data provinsi statis sebagai fallback
    private array $staticProvinces = [
        ['province_id' => '1',  'province' => 'Bali'],
        ['province_id' => '2',  'province' => 'Bangka Belitung'],
        ['province_id' => '3',  'province' => 'Banten'],
        ['province_id' => '4',  'province' => 'Bengkulu'],
        ['province_id' => '5',  'province' => 'DI Yogyakarta'],
        ['province_id' => '6',  'province' => 'DKI Jakarta'],
        ['province_id' => '7',  'province' => 'Gorontalo'],
        ['province_id' => '8',  'province' => 'Jambi'],
        ['province_id' => '9',  'province' => 'Jawa Barat'],
        ['province_id' => '10', 'province' => 'Jawa Tengah'],
        ['province_id' => '11', 'province' => 'Jawa Timur'],
        ['province_id' => '12', 'province' => 'Kalimantan Barat'],
        ['province_id' => '13', 'province' => 'Kalimantan Selatan'],
        ['province_id' => '14', 'province' => 'Kalimantan Tengah'],
        ['province_id' => '15', 'province' => 'Kalimantan Timur'],
        ['province_id' => '16', 'province' => 'Kalimantan Utara'],
        ['province_id' => '17', 'province' => 'Kepulauan Riau'],
        ['province_id' => '18', 'province' => 'Lampung'],
        ['province_id' => '19', 'province' => 'Maluku'],
        ['province_id' => '20', 'province' => 'Maluku Utara'],
        ['province_id' => '21', 'province' => 'Nusa Tenggara Barat'],
        ['province_id' => '22', 'province' => 'Nusa Tenggara Timur'],
        ['province_id' => '23', 'province' => 'Papua'],
        ['province_id' => '24', 'province' => 'Papua Barat'],
        ['province_id' => '25', 'province' => 'Riau'],
        ['province_id' => '26', 'province' => 'Sulawesi Barat'],
        ['province_id' => '27', 'province' => 'Sulawesi Selatan'],
        ['province_id' => '28', 'province' => 'Sulawesi Tengah'],
        ['province_id' => '29', 'province' => 'Sulawesi Tenggara'],
        ['province_id' => '30', 'province' => 'Sulawesi Utara'],
        ['province_id' => '31', 'province' => 'Sumatera Barat'],
        ['province_id' => '32', 'province' => 'Sumatera Selatan'],
        ['province_id' => '33', 'province' => 'Sumatera Utara'],
        ['province_id' => '34', 'province' => 'Aceh'],
    ];

    // Data kota statis per provinsi (kota-kota utama)
    private array $staticCities = [
        '1'  => [['city_id'=>'17','city_name'=>'Badung','type'=>'Kabupaten','postal_code'=>'80351'],['city_id'=>'18','city_name'=>'Bangli','type'=>'Kabupaten','postal_code'=>'80613'],['city_id'=>'99','city_name'=>'Denpasar','type'=>'Kota','postal_code'=>'80111'],['city_id'=>'272','city_name'=>'Gianyar','type'=>'Kabupaten','postal_code'=>'80511'],['city_id'=>'312','city_name'=>'Jembrana','type'=>'Kabupaten','postal_code'=>'82218'],['city_id'=>'327','city_name'=>'Karangasem','type'=>'Kabupaten','postal_code'=>'80811'],['city_id'=>'380','city_name'=>'Klungkung','type'=>'Kabupaten','postal_code'=>'80711'],['city_id'=>'499','city_name'=>'Tabanan','type'=>'Kabupaten','postal_code'=>'82111'],['city_id'=>'245','city_name'=>'Buleleng','type'=>'Kabupaten','postal_code'=>'81113']],
        '6'  => [['city_id'=>'152','city_name'=>'Jakarta Barat','type'=>'Kota','postal_code'=>'11220'],['city_id'=>'153','city_name'=>'Jakarta Pusat','type'=>'Kota','postal_code'=>'10540'],['city_id'=>'154','city_name'=>'Jakarta Selatan','type'=>'Kota','postal_code'=>'12230'],['city_id'=>'155','city_name'=>'Jakarta Timur','type'=>'Kota','postal_code'=>'13330'],['city_id'=>'156','city_name'=>'Jakarta Utara','type'=>'Kota','postal_code'=>'14140'],['city_id'=>'235','city_name'=>'Kepulauan Seribu','type'=>'Kabupaten','postal_code'=>'14550']],
        '9'  => [['city_id'=>'22','city_name'=>'Bandung','type'=>'Kota','postal_code'=>'40111'],['city_id'=>'23','city_name'=>'Bandung','type'=>'Kabupaten','postal_code'=>'40311'],['city_id'=>'74','city_name'=>'Bogor','type'=>'Kota','postal_code'=>'16111'],['city_id'=>'75','city_name'=>'Bogor','type'=>'Kabupaten','postal_code'=>'16911'],['city_id'=>'106','city_name'=>'Cimahi','type'=>'Kota','postal_code'=>'40511'],['city_id'=>'107','city_name'=>'Cirebon','type'=>'Kota','postal_code'=>'45111'],['city_id'=>'473','city_name'=>'Sukabumi','type'=>'Kota','postal_code'=>'43111'],['city_id'=>'504','city_name'=>'Tasikmalaya','type'=>'Kota','postal_code'=>'46111'],['city_id'=>'396','city_name'=>'Bekasi','type'=>'Kota','postal_code'=>'17111'],['city_id'=>'397','city_name'=>'Depok','type'=>'Kota','postal_code'=>'16411']],
        '10' => [['city_id'=>'232','city_name'=>'Semarang','type'=>'Kota','postal_code'=>'50111'],['city_id'=>'497','city_name'=>'Solo','type'=>'Kota','postal_code'=>'57111'],['city_id'=>'209','city_name'=>'Magelang','type'=>'Kota','postal_code'=>'56111'],['city_id'=>'446','city_name'=>'Salatiga','type'=>'Kota','postal_code'=>'50711'],['city_id'=>'188','city_name'=>'Kudus','type'=>'Kabupaten','postal_code'=>'59311'],['city_id'=>'520','city_name'=>'Tegal','type'=>'Kota','postal_code'=>'52111']],
        '11' => [['city_id'=>'444','city_name'=>'Surabaya','type'=>'Kota','postal_code'=>'60111'],['city_id'=>'210','city_name'=>'Malang','type'=>'Kota','postal_code'=>'65111'],['city_id'=>'257','city_name'=>'Kediri','type'=>'Kota','postal_code'=>'64111'],['city_id'=>'146','city_name'=>'Jember','type'=>'Kabupaten','postal_code'=>'68111'],['city_id'=>'37','city_name'=>'Batu','type'=>'Kota','postal_code'=>'65311'],['city_id'=>'80','city_name'=>'Blitar','type'=>'Kota','postal_code'=>'66111'],['city_id'=>'126','city_name'=>'Gresik','type'=>'Kabupaten','postal_code'=>'61111'],['city_id'=>'218','city_name'=>'Mojokerto','type'=>'Kota','postal_code'=>'61311'],['city_id'=>'246','city_name'=>'Sidoarjo','type'=>'Kabupaten','postal_code'=>'61211']],
        '5'  => [['city_id'=>'399','city_name'=>'Yogyakarta','type'=>'Kota','postal_code'=>'55111'],['city_id'=>'26','city_name'=>'Bantul','type'=>'Kabupaten','postal_code'=>'55711'],['city_id'=>'127','city_name'=>'Gunung Kidul','type'=>'Kabupaten','postal_code'=>'55811'],['city_id'=>'195','city_name'=>'Kulon Progo','type'=>'Kabupaten','postal_code'=>'55611'],['city_id'=>'475','city_name'=>'Sleman','type'=>'Kabupaten','postal_code'=>'55511']],
        '27' => [['city_id'=>'226','city_name'=>'Makassar','type'=>'Kota','postal_code'=>'90111'],['city_id'=>'260','city_name'=>'Pare-pare','type'=>'Kota','postal_code'=>'91122'],['city_id'=>'219','city_name'=>'Palopo','type'=>'Kota','postal_code'=>'91921']],
        '31' => [['city_id'=>'261','city_name'=>'Padang','type'=>'Kota','postal_code'=>'25111'],['city_id'=>'262','city_name'=>'Padang Panjang','type'=>'Kota','postal_code'=>'27111'],['city_id'=>'265','city_name'=>'Bukittinggi','type'=>'Kota','postal_code'=>'26111']],
        '33' => [['city_id'=>'215','city_name'=>'Medan','type'=>'Kota','postal_code'=>'20111'],['city_id'=>'62','city_name'=>'Binjai','type'=>'Kota','postal_code'=>'20711'],['city_id'=>'263','city_name'=>'Pematang Siantar','type'=>'Kota','postal_code'=>'21111']],
        '3'  => [['city_id'=>'39','city_name'=>'Tangerang','type'=>'Kota','postal_code'=>'15111'],['city_id'=>'40','city_name'=>'Tangerang Selatan','type'=>'Kota','postal_code'=>'15311'],['city_id'=>'41','city_name'=>'Serang','type'=>'Kota','postal_code'=>'42111'],['city_id'=>'42','city_name'=>'Cilegon','type'=>'Kota','postal_code'=>'42411']],
        '25' => [['city_id'=>'264','city_name'=>'Pekanbaru','type'=>'Kota','postal_code'=>'28111'],['city_id'=>'97','city_name'=>'Dumai','type'=>'Kota','postal_code'=>'28811']],
        '8'  => [['city_id'=>'149','city_name'=>'Jambi','type'=>'Kota','postal_code'=>'36111']],
        '18' => [['city_id'=>'196','city_name'=>'Bandar Lampung','type'=>'Kota','postal_code'=>'35111'],['city_id'=>'214','city_name'=>'Metro','type'=>'Kota','postal_code'=>'34111']],
        '32' => [['city_id'=>'266','city_name'=>'Palembang','type'=>'Kota','postal_code'=>'30111'],['city_id'=>'267','city_name'=>'Prabumulih','type'=>'Kota','postal_code'=>'31111'],['city_id'=>'268','city_name'=>'Lubuklinggau','type'=>'Kota','postal_code'=>'31611']],
        '4'  => [['city_id'=>'70','city_name'=>'Bengkulu','type'=>'Kota','postal_code'=>'38111']],
        '34' => [['city_id'=>'1','city_name'=>'Banda Aceh','type'=>'Kota','postal_code'=>'23111'],['city_id'=>'2','city_name'=>'Sabang','type'=>'Kota','postal_code'=>'23511'],['city_id'=>'3','city_name'=>'Langsa','type'=>'Kota','postal_code'=>'24411']],
        '12' => [['city_id'=>'91','city_name'=>'Pontianak','type'=>'Kota','postal_code'=>'78111'],['city_id'=>'233','city_name'=>'Singkawang','type'=>'Kota','postal_code'=>'79111']],
        '13' => [['city_id'=>'19','city_name'=>'Banjarmasin','type'=>'Kota','postal_code'=>'70111'],['city_id'=>'20','city_name'=>'Banjarbaru','type'=>'Kota','postal_code'=>'70711']],
        '14' => [['city_id'=>'92','city_name'=>'Palangkaraya','type'=>'Kota','postal_code'=>'73111']],
        '15' => [['city_id'=>'213','city_name'=>'Samarinda','type'=>'Kota','postal_code'=>'75111'],['city_id'=>'93','city_name'=>'Balikpapan','type'=>'Kota','postal_code'=>'76111'],['city_id'=>'94','city_name'=>'Bontang','type'=>'Kota','postal_code'=>'75311']],
        '16' => [['city_id'=>'95','city_name'=>'Tarakan','type'=>'Kota','postal_code'=>'77111']],
        '30' => [['city_id'=>'96','city_name'=>'Manado','type'=>'Kota','postal_code'=>'95111'],['city_id'=>'97','city_name'=>'Bitung','type'=>'Kota','postal_code'=>'95411'],['city_id'=>'98','city_name'=>'Kotamobagu','type'=>'Kota','postal_code'=>'95711']],
        '28' => [['city_id'=>'269','city_name'=>'Palu','type'=>'Kota','postal_code'=>'94111']],
        '29' => [['city_id'=>'270','city_name'=>'Kendari','type'=>'Kota','postal_code'=>'93111'],['city_id'=>'271','city_name'=>'Baubau','type'=>'Kota','postal_code'=>'93711']],
        '26' => [['city_id'=>'272','city_name'=>'Mamuju','type'=>'Kota','postal_code'=>'91511']],
        '7'  => [['city_id'=>'273','city_name'=>'Gorontalo','type'=>'Kota','postal_code'=>'96111']],
        '19' => [['city_id'=>'274','city_name'=>'Ambon','type'=>'Kota','postal_code'=>'97111'],['city_id'=>'275','city_name'=>'Tual','type'=>'Kota','postal_code'=>'97611']],
        '20' => [['city_id'=>'276','city_name'=>'Ternate','type'=>'Kota','postal_code'=>'97711'],['city_id'=>'277','city_name'=>'Tidore Kepulauan','type'=>'Kota','postal_code'=>'97811']],
        '21' => [['city_id'=>'278','city_name'=>'Mataram','type'=>'Kota','postal_code'=>'83111'],['city_id'=>'279','city_name'=>'Bima','type'=>'Kota','postal_code'=>'84111']],
        '22' => [['city_id'=>'280','city_name'=>'Kupang','type'=>'Kota','postal_code'=>'85111']],
        '23' => [['city_id'=>'281','city_name'=>'Jayapura','type'=>'Kota','postal_code'=>'99111']],
        '24' => [['city_id'=>'282','city_name'=>'Sorong','type'=>'Kota','postal_code'=>'98411'],['city_id'=>'283','city_name'=>'Manokwari','type'=>'Kota','postal_code'=>'98311']],
        '2'  => [['city_id'=>'284','city_name'=>'Pangkal Pinang','type'=>'Kota','postal_code'=>'33111']],
        '17' => [['city_id'=>'285','city_name'=>'Batam','type'=>'Kota','postal_code'=>'29411'],['city_id'=>'286','city_name'=>'Tanjung Pinang','type'=>'Kota','postal_code'=>'29111']],
    ];

    public function __construct()
    {
        $this->apiKey  = config('services.rajaongkir.api_key', '');
        $this->baseUrl = config('services.rajaongkir.base_url', '');
    }

    public function getProvinces(): array
    {
        // Coba API dulu, fallback ke data statis
        if (!empty($this->apiKey)) {
            try {
                $res = Http::timeout(5)->withHeaders(['key' => $this->apiKey])
                    ->get($this->baseUrl . '/province');
                if ($res->successful()) {
                    return Cache::remember('provinces', 86400, fn() => $res->json('rajaongkir.results') ?? []);
                }
            } catch (\Exception $e) {
                Log::warning('RajaOngkir province API failed: ' . $e->getMessage());
            }
        }
        // Fallback data statis
        return $this->staticProvinces;
    }

    public function getCities(int $provinceId): array
    {
        // Coba API dulu, fallback ke data statis
        if (!empty($this->apiKey)) {
            try {
                $res = Http::timeout(5)->withHeaders(['key' => $this->apiKey])
                    ->get($this->baseUrl . '/city', ['province' => $provinceId]);
                if ($res->successful()) {
                    return Cache::remember("cities_{$provinceId}", 86400, fn() => $res->json('rajaongkir.results') ?? []);
                }
            } catch (\Exception $e) {
                Log::warning('RajaOngkir city API failed: ' . $e->getMessage());
            }
        }
        // Fallback data statis
        return $this->staticCities[(string)$provinceId] ?? [
            ['city_id' => '0', 'city_name' => 'Kota Utama', 'type' => 'Kota', 'postal_code' => '00000']
        ];
    }

    public function calculate(int $originCityId, int $destinationCityId, int $weightGram, string $courier): array
    {
        // Coba API dulu
        if (!empty($this->apiKey)) {
            try {
                $res = Http::timeout(5)->withHeaders(['key' => $this->apiKey])
                    ->post($this->baseUrl . '/cost', [
                        'origin'      => $originCityId,
                        'destination' => $destinationCityId,
                        'weight'      => $weightGram,
                        'courier'     => strtolower($courier),
                    ]);
                if ($res->successful()) {
                    $results = $res->json('rajaongkir.results.0.costs') ?? [];
                    if (!empty($results)) {
                        return collect($results)->map(fn($r) => [
                            'service'     => $r['service'],
                            'description' => $r['description'],
                            'cost'        => $r['cost'][0]['value'],
                            'etd'         => $r['cost'][0]['etd'],
                        ])->toArray();
                    }
                }
            } catch (\Exception $e) {
                Log::warning('RajaOngkir cost API failed: ' . $e->getMessage());
            }
        }

        // Fallback: hitung ongkir estimasi berdasarkan berat
        return $this->estimateShipping($weightGram, $courier);
    }

    /**
     * Estimasi ongkir manual jika API tidak tersedia
     */
    private function estimateShipping(int $weightGram, string $courier): array
    {
        $kg        = max(1, ceil($weightGram / 1000));
        $baseRates = [
            'jne'     => ['REG' => 9000,  'YES' => 19000, 'OKE' => 7000],
            'jnt'     => ['Reguler' => 8000, 'Express' => 15000],
            'pos'     => ['Kilat Khusus' => 9000, 'Biasa' => 6000],
            'sicepat' => ['HALU' => 8500, 'BEST' => 7000],
        ];

        $courierKey = strtolower($courier);
        $rates      = $baseRates[$courierKey] ?? $baseRates['jne'];
        $services   = [];

        foreach ($rates as $service => $pricePerKg) {
            $cost = $pricePerKg * $kg;
            $services[] = [
                'service'     => $service,
                'description' => strtoupper($courierKey) . ' ' . $service,
                'cost'        => $cost,
                'etd'         => match($service) {
                    'YES', 'Express', 'Kilat Khusus', 'HALU' => '1-2',
                    'REG', 'Reguler', 'BEST' => '2-4',
                    default => '3-7',
                },
            ];
        }

        return $services;
    }
}