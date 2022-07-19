## v1.1.8

- App/LaraJson helpers klasörü ve dosyası oluşturuldu


## v1.1.6

- Komut satırı ile kurulum eklendi (php artisan salih:install --path={Json File Path})


## v1.1.5

-composer.json dosyasına
    "extra": {
        "laravel": {
            "providers": [
                "Salih\\Composer\\LaraJsonServiceProvider"
            ]
        }
    },
eklendi.


## v1.1.2

- Static kullanım kaldırıldı.
- php artisan salih komutu eklendi
- Console/...  klasörü test edildi

## v1.1.1

- Static ve instance kullanımı birlikte sunuldu ileride seçilen ile yola devam edilecek.


## v1.1.0

- Static kullanıma alternatif olarak instance yapısı oluşturuldu.


## v1.0.6

- deleteStub() fonksiyonu oluşturuldu.
