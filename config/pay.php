<?php

return [
    'alipay'=>[
        'app_id'=>'2016092700608186',
        'ali_public_key'=>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwjgCnL8/8O1V24JmKjKRzzY8nlQrXnFTIzcIbxPJ+ezsgNLomGLDbrOtHq+rBuTS8FN4piG89/wEs7FMJcSA3M2LAGAkXTz+/TUd1XqK1VOzsmthLpiIrpHYSki8pyjh3hcsPJqyofOkZOIk2aGFUkS0k/p0WBTnGHJfndCwK0FeR4wl0xyfUjKtGx5XOwTsAghPeFlEP7mkII0lIpJB8GWxUpZZQOXP3+si4TqePoEnJojjE0hE5nM1f4QHmfJbJOHI52awk3lVN4EgDS1QvwfTyoDhTNUKOS2bbN5uurp8xubK27D5oME4EDPzG7CRBRKbSYHZla8k/257+eezIwIDAQAB',
        'private_key'=>'MIIEowIBAAKCAQEA9QIs+yneYz4fdZtPmEzgw1iXgexQszWu7dRwlphDHC9vNRyqGpWbytqk817k8jj1lM80iFZ6bSt4hA9+catzT00INVkEF48uIE1hJldUEOew50bkkPNaBFiUF/UgH+Xs+r/ip7PqafVHm6+rO7c4BBgQI3unoW9kWfjHPB1qnCgoWB9jbo/wbFUMk6qA2/9Brr0485+C8r50NLdgvJI5QghJyxRPntibBbiZqNfuFNasL455bfopbVe738G9O7H1M5X65B0uzhUIuxitaC1GqHoWPMn9aFnVhulLSzHhvFy87weCu9hdOr3HqI0Jc4acObbKA09SCs92zfOJhMfjVwIDAQABAoIBAHNqCvZlyrTFVtx3xQ7haB+dZAF36KwC5dxy7naCU1q7nY9PaXIMd88fIATk9sSDwGvRD9YJprI2gBb1lYLGWRNKHkvDGwJeGERvLwx4pskv202XP6Rofkb90wVWG0Prc67LJWIKhqpTOOBF6EvBC0oia7fLUDPj3UnkvzW6RbY46Irr5mhFje5o1+7WQOoZi99tj3yyhi9JqMOuyDyg6NEmDj0fdfE2VEl4nzZx4RIhGO6MumQRb2/3qqsL65yZ7YyGEI4FlabHX/p00AUavyKCOafF8aF4/nD68Yp02sJeHPDtZr87UizngjS7zQRPJ+g+7RtOH4Vp/EahncEIoAECgYEA/uuoAGlzmaOR6Nh36M9+dRHbzhabvT8mUnDq3YrRsX5UBXNHuGh+tltlwDiewLwlFr9uJr9iCKd8c/IOdQJKwzED1LY93Wt2jrXiTyPOcwKYonSFRGXiQCF/44BSEhtTqq99PoULAl3JVjGNn4inHNSVECmrmuqV7M0KyICiugECgYEA9gvGQI5EBjdsx9rsJQPb1RiW/qWKBHFSzswpSZWU95gQFZ1GS9ilVzvDWoprAFX0sV6Q5DjP0r9L5tpXWSLeUkNrf4DPkLPGjiwMBhZf/FYQc6IPku+r7OFSCI1TVE/2b5gmGR88IOGmorgXnN1OLneVYDVb2O3chMH4tIXIrVcCgYA4EQB8RrUkH3Oh8Ko+zBQWD312kEZkkxUMoMUnQLbqAMzd/gLSLjlgRi3U3x8baMYHgMbrQsB/Asc+gQho974VvBwJlqN4pYAH1z0VBt0LMpD7egEtj/L5A+Uq1jg2v0fhjINRUtWfCZ2UlYV+hwGqN58pVODnS5z53gb3HkOsAQKBgQDTC2pz+ROUGXpvOvMPFO49LKhnqGpoQSP8SdaoTWvHrGEviQXBDQVVe+enA8+gLBqFAU5a0/g/FuLuEx6VUHlOKpJMfZqMzgdj75goqIyQjunxpXKiYH4h42tP9pkhWq1RLzqOleIneiZKsIlgfdsPtHcmXN55hh1+qjq/7XkiZQKBgBed7+dF/BhKDrDVno5S045ZYNIb718i3os8sTsYEpy9BpP8j1R0NAS2lsHAS5WAIelMIoilxjUONsGYr49xCqv9/vjt8i22Msbfn86ZkksT6js9lbQEPlnhPlUAr/o9gCvOLJ1EOE3omrErGKtsztyZ5umTLPIXjLVrR/9hQ4qK',
        'log'=>[
            'file'=>storage_path('logs/alipay.log'),
        ],
    ],
    'wechat'=>[
        'app_id'=>'wxb0f8ca1c4347edff',
        'mch_id'=>'1499492312',
        'key'=>'4NOOTs1ySePsospYk6yKjFN2mOpQCwiV',
        'ceat_client'=>resource_path('wechat_pay/apiclient_cert.pem'),
        'cert_key'=>resource_path('wechat_pay/apiclient_key.pem'),
        'log'=>[
            'file'=>storage_path('logs/wechat_pay.log'),
        ],
    ],
];
