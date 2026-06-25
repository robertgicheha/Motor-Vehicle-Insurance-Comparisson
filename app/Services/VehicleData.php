<?php

namespace App\Services;

class VehicleData
{
    protected static array $data = [
        'Toyota' => [
            'Allion', 'Alphard', 'Axio', 'Belta', 'Camry', 'Coaster', 'Corolla',
            'Crown', 'Dyna', 'Etios', 'Fielder', 'Fortuner', 'GR Yaris', 'Granvia',
            'Harrier', 'Hiace', 'Highlander', 'Hilux', 'Hilux Surf', 'Ist',
            'Kluger', 'Land Cruiser', 'Land Cruiser Prado', 'Mark X', 'Noah',
            'Passo', 'Porte', 'Prado', 'Premio', 'Probox', 'Ractis', 'Raum',
            'RAV4', 'Rumion', 'Rush', 'Sequoia', 'Sienta', 'Spade', 'Starlet',
            'Succeed', 'Supra', 'Tank', 'Tundra', 'Urban Cruiser', 'Vellfire',
            'Vitz', 'Voxy', 'Wish', 'Yaris', '4Runner',
        ],
        'Honda' => [
            'Accord', 'Amaze', 'BR-V', 'Brio', 'CR-V', 'CR-Z', 'City', 'Civic',
            'Crossroad', 'Elevate', 'Fit', 'Freed', 'Grace', 'HR-V', 'Insight',
            'Jazz', 'Mobilio', 'N-Box', 'N-One', 'N-WGN', 'Odyssey', 'S660',
            'Shuttle', 'Stepwgn', 'Stream', 'Vezel', 'WR-V',
        ],
        'Nissan' => [
            'AD', 'Bluebird', 'Caravan', 'Cima', 'Cube', 'Dualis', 'Elgrand',
            'Fairlady Z', 'Fuga', 'GT-R', 'Hardbody', 'Juke', 'Kicks', 'Lafesta',
            'Latio', 'Leaf', 'Magnite', 'March', 'Micra', 'Murano', 'NV200',
            'NV350', 'Navara', 'Note', 'Pathfinder', 'Patrol', 'Pixo',
            'Qashqai', 'Serena', 'Skyline', 'Sunny', 'Sylphy', 'Teana',
            'Terrano', 'Tida', 'Urvan', 'Wingroad', 'X-Trail',
        ],
        'Subaru' => [
            'Ascent', 'B4', 'BRZ', 'Crosstrek', 'Exiga', 'Forester', 'Impreza',
            'Legacy', 'Legacy Touring Wagon', 'Levorg', 'Liberty', 'Outback',
            'Outback Sedan', 'Solterra', 'Tribeca', 'WRX', 'WRX STI', 'XV',
        ],
        'Mitsubishi' => [
            'ASX', 'Airtrek', 'Attrage', 'Canter', 'Chariot', 'Colt', 'Delica',
            'Diamante', 'Eclipse Cross', 'Galant', 'L200', 'Lancer', 'Minicab',
            'Mirage', 'Montero', 'Outlander', 'Pajero', 'Pajero Sport', 'RVR',
            'Shogun', 'Space Star', 'Strada', 'Triton', 'Xpander', 'i-MiEV',
        ],
        'Mazda' => [
            'Atenza', 'Axela', 'BT-50', 'Biante', 'Bongo', 'CX-3', 'CX-30',
            'CX-5', 'CX-50', 'CX-60', 'CX-7', 'CX-9', 'CX-90', 'Carol',
            'Demio', 'Flair', 'MPV', 'MX-30', 'MX-5', 'Mazda 2', 'Mazda 3',
            'Mazda 5', 'Mazda 6', 'Premacy', 'RX-7', 'RX-8', 'Scrum',
        ],
        'Mercedes-Benz' => [
            'A-Class', 'AMG GT', 'B-Class', 'C-Class', 'CLA', 'CLS', 'Citan',
            'E-Class', 'EQB', 'EQC', 'EQS', 'EQE', 'G-Class', 'GLA', 'GLB',
            'GLC', 'GLE', 'GLK', 'GLS', 'ML', 'S-Class', 'SLC', 'SL',
            'Sprinter', 'V-Class', 'Vito', 'X-Class',
        ],
        'BMW' => [
            '1 Series', '2 Series', '3 Series', '5 Series', '6 Series',
            '7 Series', '8 Series', 'Active Tourer', 'Gran Tourer', 'M2', 'M3',
            'M4', 'M5', 'M8', 'X1', 'X2', 'X3', 'X3 M', 'X4', 'X4 M', 'X5',
            'X5 M', 'X6', 'X6 M', 'X7', 'Z3', 'Z4', 'Z8', 'i3', 'i4', 'i8',
            'iX', 'iX3',
        ],
        'Volkswagen' => [
            'Amarok', 'Arteon', 'Beetle', 'CC', 'Caddy', 'Caravelle', 'Crafter',
            'Eos', 'Golf', 'Golf GTI', 'Golf R', 'ID.3', 'ID.4', 'Jetta',
            'Multivan', 'Passat', 'Polo', 'Polo GTI', 'Scirocco', 'Sharan',
            'T-Cross', 'T-Roc', 'Teramont', 'Tiguan', 'Touareg', 'Touran',
            'Transporter', 'Vento',
        ],
        'Ford' => [
            'B-Max', 'Bronco', 'EcoSport', 'Edge', 'Endura', 'Escape',
            'Everest', 'Expedition', 'Explorer', 'F-150', 'Fiesta', 'Figo',
            'Focus', 'Fusion', 'Galaxy', 'Grand C-Max', 'Kuga', 'Maverick',
            'Mondeo', 'Mustang', 'Ranger', 'Ranger Rover', 'Raptor',
            'S-Max', 'Territory', 'Tourneo', 'Transit',
        ],
        'Suzuki' => [
            'APV', 'Alto', 'Baleno', 'Carry', 'Celerio', 'Ciaz', 'Dzire',
            'Ertiga', 'Every', 'Grand Vitara', 'Hustler', 'Ignis', 'Jimny',
            'Lapin', 'MR Wagon', 'S-Cross', 'S-Presso', 'Spacia', 'Swift',
            'Vitara', 'Wagon R', 'XL7', 'Escudo',
        ],
        'Isuzu' => [
            'Bighorn', 'D-Max', 'Elf', 'FRR', 'FTS', 'FVR', 'Forward',
            'G-CX', 'KB', 'MU-X', 'N-Series', 'NPR', 'Traviz', 'Trooper',
        ],
        'Land Rover' => [
            'Defender', 'Discovery', 'Discovery Sport', 'Freelander', 'LR2',
            'LR3', 'LR4', 'Range Rover', 'Range Rover Evoque',
            'Range Rover LWB', 'Range Rover Sport', 'Range Rover Sport SVR',
            'Range Rover Velar',
        ],
        'Volvo' => [
            'Amazon', 'C30', 'C40', 'C70', 'P1800', 'S40', 'S60', 'S80',
            'S90', 'V40', 'V60', 'V70', 'V90', 'XC40', 'XC60', 'XC70',
            'XC90',
        ],
        'Hyundai' => [
            'Accent', 'Atos', 'Creta', 'Elantra', 'Getz', 'Grand i10',
            'H-1', 'Ioniq', 'Ioniq 5', 'Ioniq 6', 'Kona', 'Matrix',
            'Nexo', 'Palisade', 'Porter', 'Santa Fe', 'Sonata', 'Starex',
            'Staria', 'Tucson', 'Veloster', 'Venue', 'i10', 'i20', 'ix35',
        ],
        'Kia' => [
            'Bongo', 'Carnival', 'Cerato', 'EV6', 'EV9', 'Forte', 'K2500',
            'K2700', 'K5', 'K7', 'K9', 'Mohave', 'Niro', 'Optima', 'Picanto',
            'Pregio', 'Rio', 'Seltos', 'Sonet', 'Sorento', 'Soul', 'Sportage',
            'Stonic', 'BestA',
        ],
        'Daihatsu' => [
            'Atrai', 'Ayla', 'Boon', 'Cast', 'Copen', 'Delta', 'Gran Max',
            'Hijet', 'Luxio', 'Mira', 'Move', 'Rocky', 'Sigra', 'Sirion',
            'Tanto', 'Terios', 'Wake', 'Xenia',
        ],
        'Audi' => [
            'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'Q2', 'Q3', 'Q4 e-tron',
            'Q5', 'Q6 e-tron', 'Q7', 'Q8', 'Q8 e-tron', 'R8', 'RS3', 'RS4',
            'RS5', 'RS6', 'RS7', 'S3', 'S4', 'S5', 'S6', 'S7', 'S8', 'TT',
            'e-tron',
        ],
        'Peugeot' => [
            '106', '206', '207', '307', '308', '4007', '4008', '406', '407',
            '508', '607', '2008', '3008', '5008', 'Boxer', 'Expert',
            'Landtrek', 'Partner', 'RCZ', 'Rifter', 'Traveller',
        ],
        'Renault' => [
            'Arkana', 'Captur', 'Clio', 'Duster', 'Kadjar', 'Kangoo',
            'Kiger', 'Koleos', 'Kwid', 'Master', 'Megane', 'Scenic',
            'Talisman', 'Trafic', 'Triber', 'Zoe', 'Grand Scenic',
        ],
        'Mahindra' => [
            'Bolero', 'KUV100', 'Marazzo', 'NEO', 'Pik-Up', 'Quanto',
            'Scorpio', 'Thar', 'TUV300', 'XUV300', 'XUV500', 'XUV700',
        ],
        'Tata' => [
            'Ace', 'Indica', 'Indigo', 'Manza', 'Nano', 'Nexon', 'Punch',
            'Safari', 'Sumo', 'Super Ace', 'Tiago', 'Tigor', 'Xenon',
            'Zest',
        ],
        'DFSK' => [
            'C-Series', 'F-Series', 'Glory', 'K-Series', 'Seres',
        ],
        'Great Wall' => [
            'Haval H6', 'Haval Jolion', 'Haval M6', 'Ora', 'Poer', 'Steed',
            'Wingle', 'Wingle 7',
        ],
        'Chery' => [
            'Arrizo 5', 'Arrizo 6', 'Fulwin 2', 'QQ', 'Tiggo 2', 'Tiggo 4',
            'Tiggo 7', 'Tiggo 8', 'Tiggo 8 Pro',
        ],
        'BYD' => [
            'Atto 3', 'Dolphin', 'e6', 'Han', 'Seal', 'Song Plus', 'Tang',
            'Yuan Plus',
        ],
        'NIO' => ['EL6', 'EL7', 'ES6', 'ES8', 'ET5', 'ET7'],
        'Proton' => [
            'Gen-2', 'Inspira', 'Iriz', 'Persona', 'Prevé', 'Saga', 'Waja',
            'X50', 'X70', 'X90',
        ],
        'Perodua' => [
            'Alza', 'Axia', 'Bezza', 'Myvi', 'Viva',
        ],
        'Bajaj' => [
            'Qute', 'RE60',
        ],
    ];

    public static function makes(): array
    {
        $names = array_keys(static::$data);
        sort($names);
        return $names;
    }

    public static function models(string $make): array
    {
        $make = ucwords(strtolower(trim($make)));
        return static::$data[$make] ?? [];
    }

    public static function all(): array
    {
        return static::$data;
    }
}
