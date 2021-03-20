<?php
require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Str;

function ask_question( $question, $default = null, $exclude = [] ) {
    echo $question;
    if ( ! empty( $default ) ) {
        echo ' [' . $default . ']';
    }
    echo ': ';
    $input = trim( fgets( STDIN ) );

    if ( empty( $input ) && ! empty( $default ) ) {
        $input = $default;
    }

    return $input;
}

function confirm( $question, $default = null ) {
    echo $question . ' (Yes|Y|No|N)';
    if ( $default === false ) {
        echo ' [No]';
    } else if ( $default === true ) {
        echo ' [Yes]';
    }

    $input = strtolower( trim( fgets( STDIN ) ) );

    if ( empty( $input ) && ! is_null( $default ) ) {
        $input = $default ? 'y' : 'n';
    }

    if ( $input === 'y' || $input === 'yes' ) {
        $input = true;
    } elseif ( $input === 'n' || $input === 'no' ) {
        $input = false;
    } else {
        echo "Invalid input. Try again\n";

        return confirm( $question, $default );
    }

    return $input;
}

function rglob( $pattern, $flags = 0, $exclude = [] ) {
    $files = glob( $pattern, $flags );
    foreach ( glob( dirname( $pattern ) . '/*', GLOB_ONLYDIR | GLOB_NOSORT ) as $dir ) {
        if ( ! in_array( $dir, $exclude ) ) {
            $files = array_merge( $files, rglob( $dir . '/' . basename( $pattern ), $flags ) );
        }
    }

    return array_filter(
        $files, function ( $file ) use ( $exclude ) {
        return empty(
        array_filter(
            $exclude, function ( $pattern ) use ( $file ) {
            return ! empty( preg_match( $pattern, $file ) );
        }
        )
        );
    }
    );
}

$git_name              = trim( shell_exec( "git config user.name" ) );
$author_name           = ask_question( 'Author name', $git_name );
$author_username_guess = Str::studly( $author_name );
$author_username       = ask_question( 'Author Username', $author_username_guess );

$git_email       = trim( shell_exec( "git config user.email" ) );
$author_email    = ask_question( 'Author mail', $git_email );
$author_homepage = 'https://' . Str::afterLast( $author_email, '@' );


$pwd         = getcwd();
$package_dir = basename( $pwd, '/' );


$vendor_name_guess  = Str::studly( $author_name );
$vendor_name        = ask_question( 'Vendor name', $vendor_name_guess );
$vendor_name_studly = Str::studly( $vendor_name );
$vendor_name_snake  = Str::snake( $vendor_name_studly );
$vendor_name_slug   = Str::slug( $vendor_name_snake );

$package_name_guess  = Str::studly( $package_dir );
$package_name        = ask_question( 'Package name', $package_name_guess );
$package_description = ask_question( "Package description", $package_name );
$package_name_studly = Str::studly( $package_name );
$package_name_snake  = Str::snake( $package_name_studly );
$package_name_slug   = Str::slug( $package_name_snake );

$short_name_guess  = Str::after( $package_name_slug, 'laravel-' );
$short_name        = ask_question( 'Short package name', $short_name_guess );
$short_name_studly = Str::studly( $short_name );
$short_name_snake  = Str::snake( $short_name_studly );
$short_name_slug   = Str::slug( $short_name_snake );

$namespace = $vendor_name_studly . '\\' . $short_name_studly;
$date      = date( 'Y-m-d' );
$replace   = [
    'Matthias Wohlgemuth'      => $author_name,
    ':author_name'             => $author_name,

    'wohlgemu'                 => $author_username,
    ':author_username'         => $author_username,

    'wohlgemuth@schildhain.de' => $author_email,
    'author@vendor.com'        => $author_email,
    ':author_email'            => $author_email,
    ':author_homepage'         => $author_homepage,

    'LaravelPackageSkeleton'   => $package_name_studly,
    ':PackageName'             => $package_name_studly,
    'laravel_package_skeleton' => $package_name_snake,
    ':package_name'            => $package_name_snake,
    'laravel-package-skeleton' => $package_name_slug,
    ':package-name'            => $package_name_slug,


    'PackageSkeleton'     => $short_name_studly,
    ':PackageShortName'   => $short_name_studly,
    'package-skeleton'    => $short_name_slug,
    ':package-short-name' => $short_name_slug,
    'package_skeleton'    => $short_name_snake,
    ':package_short_name' => $short_name_snake,

    'Laravel Package Skeleton' => $package_description,
    ':package_description'     => $package_description,

    ':namespace' => $namespace,
    ':date'      => $date,

    'Schildhain'   => $vendor_name_studly,
    ':VendorName'  => $vendor_name_studly,
    'schildhain'   => $vendor_name_slug,
    ':vendor-name' => $vendor_name_slug,
    '_schildhain_' => '_' . $vendor_name_snake . '_',
    '_schildhain'  => '_' . $vendor_name_snake,
    'schildhain_'  => $vendor_name_snake . '_',
    ':vendor_name' => $vendor_name_snake,
];

print_r( $replace );
collect( rglob( "*", GLOB_BRACE, [ '/vendor/' ] ) )->each(
    function ( $name ) use ( $replace ) {
        if ( ( $newname = strtr( $name, $replace ) ) !== $name ) {
            rename( $name, $newname );
            $name = $newname;
        }

        if ( is_file( $name ) ) {
            $contents = file_get_contents( $name );
            $contents = strtr( $contents, $replace );
            file_put_contents( $name, $contents );
        }
    }
);

if ( confirm( 'Delete package configuration script?', true ) ) {
    unlink( __FILE__ );
    echo 'Deleted ' . __FILE__ . "\n";
}
echo "\n";
