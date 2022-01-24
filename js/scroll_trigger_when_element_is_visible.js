
/* The Intersection Observer API makes it possible to know when an element enters or exits the browser viewport. There is no need to attach any event handlers like scroll triggers (example below). Furthermore, this API is asynchronous — monitoring element for visibility is not done on the main thread. This is a major performance boost. */

var observer = new IntersectionObserver(function (entries) {
    // isIntersecting is true when element and viewport are overlapping
    // isIntersecting is false when element and viewport don't overlap
    if (entries[0].isIntersecting === true)
        console.log('Element has just become visible in screen');
// an threshold: [1] would mean that the Element is fully visible on screen
}, { threshold: [0] });

observer.observe(document.querySelector("#main-container"));



/* the old way - with scroll Event (not good for performance) */
window.addEventListener('scroll', function () {
    var element = document.querySelector('#elite-pricing-card');
    var position = element.getBoundingClientRect();

    if(position.top >= 0 && position.bottom <= window.innerHeight) {
        console.log('Element is fully visible in screen');
    }
    if(position.top < window.innerHeight && position.bottom >= 0) {
        console.log('Element is partially visible in screen');
    }
});
