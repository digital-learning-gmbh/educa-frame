<?php

namespace App\Http\Controllers\API\V1\Profil;

use App\Http\Controllers\API\ApiController;
use Carbon\Carbon;
use Chiiya\LaravelPasses\PassBuilder;
use Chiiya\Passes\Apple\Components\Barcode;
use Chiiya\Passes\Apple\Components\Field;
use Chiiya\Passes\Apple\Components\Image;
use Chiiya\Passes\Apple\Components\SecondaryField;
use Chiiya\Passes\Apple\Enumerators\BarcodeFormat;
use Chiiya\Passes\Apple\Enumerators\ImageType;
use Chiiya\Passes\Apple\Passes\Coupon;
use Chiiya\Passes\Apple\Passes\GenericPass;
use Chiiya\Passes\Apple\PassFactory;
use Chiiya\Passes\Google\Components\Common\GroupingInfo;
use Chiiya\Passes\Google\Components\Common\ImageModuleData;
use Chiiya\Passes\Google\Components\Common\LinksModuleData;
use Chiiya\Passes\Google\Components\Common\LocalizedString;
use Chiiya\Passes\Google\Components\Common\TextModuleData;
use Chiiya\Passes\Google\Components\Common\TimeInterval;
use Chiiya\Passes\Google\Components\Common\Uri;
use Chiiya\Passes\Google\Components\Generic\Notifications;
use Chiiya\Passes\Google\Components\Generic\UpcomingNotification;
use Chiiya\Passes\Google\Enumerators\BarcodeRenderEncoding;
use Chiiya\Passes\Google\Enumerators\BarcodeType;
use Chiiya\Passes\Google\Enumerators\MultipleDevicesAndHoldersAllowedStatus;
use Chiiya\Passes\Google\Enumerators\State;
use Chiiya\Passes\Google\Passes\GenericClass;
use Chiiya\Passes\Google\Passes\GenericObject;
use Chiiya\Passes\Google\Components\Common\DateTime;
use Chiiya\Passes\Google\Components\Common\Image as ImageGoogle;
use Chiiya\Passes\Google\Repositories\GenericClassRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Chiiya\Passes\Google\Enumerators\BarcodeRenderEncoding as GoogleBarcodeRenderEncoding;
use Chiiya\Passes\Google\Enumerators\BarcodeType as GoogleBarcodeType;
use Chiiya\Passes\Google\Components\Common\Barcode as GoogleBarcode;

class EducaPassController extends ApiController
{
    public function __construct(
        private PassBuilder $builder,
        private GenericClassRepository $offers,
    ) {
        $this->builder = $builder;
    }

    public function downloadApple(Request $request)
    {
        $tokenString = trim($request->bearerToken() ? $request->bearerToken() : ($request->input("token") ? $request->input("token") : $request->cookie("token")));
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        $pass = new GenericPass(
            description: 'educa Teilnehmenderausweis',
            organizationName: 'Digital Learning GmbH',
            passTypeIdentifier: 'pass.gmbh.digitallearning.educa',
            serialNumber: '1464194291627',
            teamIdentifier: '98D65NZRJF',
            backgroundColor: 'rgb(242,243,245)',
            foregroundColor: 'rgb(33,37,41)',
            labelColor: 'rgb(33,37,41)',
            webServiceURL: 'https://digitallearning.gmbh',
            authenticationToken: $tokenString,
            sharingProhibited: true,
            logoText: "232323",
            expirationDate: Carbon::now()->addMonth()->toIso8601String(),
            headerFields: [
                new SecondaryField(key: 'until', value: '23.10.2024', label: 'Gültig bis'),
            ],
            primaryFields: [
                new Field(key: 'name', value: $cloud_user->name, label: 'Name'),
            ],
            secondaryFields: [
                new SecondaryField(key: 'birthday', value: '04.03.96', label: 'Geburtsdatum'),
                new SecondaryField(key: 'tn_number3', value: 'Wohnung A', label: 'Unterbringung'),
                new SecondaryField(key: 'tn_number3', value: 'Ausbildung Fachinformatiker', label: 'Maßnahme'),
            ],
            backFields: [
                new Field(key: 'terms', value: 'Berufsbildungswerk Südhessen gGmbH', label: 'Kontakt'),
                new Field(key: 'phone', value: '+492323', label: 'Mobil'),
            ],
            barcode: new Barcode(
                format: BarcodeFormat::QR,
                messageEncoding: BarcodeRenderEncoding::UTF_8,
                message: '1464194291627',
            )
        );

        $pass
       //     ->addImage(new Image(base_path("/storage/images/user/" . $cloud_user->image . ".png"), ImageType::THUMBNAIL, 1))
//            ->addImage(new Image('strip-2x.png', ImageType::STRIP, 2))
//            ->addImage(new Image('strip-3x.png', ImageType::STRIP, 3))
             ->addImage(new Image(base_path("resources/educaPass/icon.png"), ImageType::LOGO))
             ->addImage(new Image(base_path("resources/educaPass/icon.png"), ImageType::ICON))
//            ->addImage(new Image('icon@2x.png', ImageType::ICON, 2))
//            ->addImage(new Image('icon@3x.png', ImageType::ICON, 3))
;

        $path = $this->builder->apple()->create($pass, 'educaPass');

        return Storage::disk(config('passes.apple.disk'))->download($path);
    }

    public function downloadAndroid(Request $request)
    {
        $tokenString = trim($request->bearerToken() ? $request->bearerToken() : ($request->input("token") ? $request->input("token") : $request->cookie("token")));
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
//        $class2 = new GenericClass(
//            id: '3388000000022352908.generic-object',
//            multipleDevicesAndHoldersAllowedStatus: MultipleDevicesAndHoldersAllowedStatus::MULTIPLE_HOLDERS,
//            linksModuleData: new LinksModuleData(
//                uris: [
//                    new Uri(uri: 'https://example.org/app', description: 'App'),
//                    new Uri(uri: 'https://example.org', description: 'Homepage'),
//                ]
//            ),
//            imageModulesData: [
//                new ImageModuleData(
//                    mainImage: ImageGoogle::make('https://digitallearning.gmbh/wp-content/uploads/2023/01/neural.png')
//                )
//            ],
//            textModulesData: [
//                new TextModuleData(
//                    header: 'Lorem ipsum',
//                    body: 'Dolor sit amet'
//                )
//            ]
//        );
//        $this->offers->create($class2);

        $object = new GenericObject(
            classId: '3388000000022352908.generic-object',
            id: '3388000000022352908.'.Str::uuid()->toString(),
            cardTitle: LocalizedString::make('de', '232323'),
            header: LocalizedString::make('de', $cloud_user->name),
            logo: ImageGoogle::make('https://digitallearning.gmbh/wp-content/uploads/2023/01/neural.png'),
            hexBackgroundColor: '#f2f3f5',
            state: State::ACTIVE,
            validTimeInterval: new TimeInterval(
                start: new DateTime(date: now()),
                end: new DateTime(date: now()->addMonth())
            ),
            notifications: new Notifications(
                upcomingNotification: new UpcomingNotification(
                    enableNotification: true
                ),
            ),
            textModulesData: [
                new TextModuleData(
                    id: 'key-1',
                    header: 'label-1',
                    body: 'value-1',
                ),
                new TextModuleData(
                    id: 'key-2',
                    header: 'label-2',
                    body: 'value-2',
                )
            ],
            barcode: new GoogleBarcode(
                type: GoogleBarcodeType::QR_CODE,
                renderEncoding: GoogleBarcodeRenderEncoding::UTF_8,
                value: '1464194291627',
            ),
        );

        $path = $this->builder->google()->createJWT()->addGenericObject($object)->sign();

       // print_r(json_encode($path,JSON_PRETTY_PRINT));
       // die();
        return redirect("https://pay.google.com/gp/v/save/" . $path);
    }
}
