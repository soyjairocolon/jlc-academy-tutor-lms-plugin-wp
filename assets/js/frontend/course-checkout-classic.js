(function () {

    if (
        typeof jlcCheckout === 'undefined' ||
        !jlcCheckout.isCourseCheckout
    ) {
        return;
    }

    console.log('========== JLC Checkout ==========');

    console.log(window.wc);

    console.log(window.wc.blocksCheckout);

    console.log(window.wc.blocksCheckout?.registerCheckoutFilters);

    console.log(window.wc.blocksCheckout?.registerCheckoutExtensionCallbacks);

    console.log(window.wc.blocksCheckout?.registerCheckoutBlock);

    console.log(window.wc.blocksCheckout?.registerCheckoutBlockExtension);

    console.log(window.wc.blocksCheckout?.registerAdditionalCheckoutFields);

})();




















// wp.domReady(() => {

//     console.log('JLC Checkout Blocks cargado');

//     console.log('wp.data', wp.data);

//     console.log(
//         'Store checkout',
//         wp.data.select('wc/store/checkout')
//     );

//     console.log(
//         'Store cart',
//         wp.data.select('wc/store/cart')
//     );

//     console.log(
//         'Checkout Data',
//         wp.data.select('wc/store/checkout')?.getCheckoutData?.()
//     );

//     console.log(
//         'Cart Data',
//         wp.data.select('wc/store/cart')?.getCartData?.()
//     );

// });