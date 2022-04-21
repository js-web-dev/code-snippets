
/* The Intersection Observer API makes it possible to know when an element enters or exits the browser viewport. There is no need to attach any event handlers like scroll triggers (example below). Furthermore, this API is asynchronous — monitoring element for visibility is not done on the main thread. This is a major performance boost. */

/* set Options if needed */
const myObserverOptions = {
    root: null, // normally body but mayby you need a custom viewport
    rootMargin: "200px", // kind of an offset
    threshold: [0], // threshold: [1] would mean that the Element is fully visible on screen
};

/* define the callback function */
const myObserverCallback = function (entries, observer) {
    log('c9Animation Observer attached!');
    entries.forEach(entry => {
        /*  isIntersecting is true when element and viewport are overlapping
            isIntersecting is false when element and viewport don't overlap */
        if (entry.isIntersecting) {
            /* ... do magic things */
            console.log('Element has just become visible in screen');
            /* if you dont need the observer anymore - disconnect it */
            observer.disconnect();
        }
    })
}

/* instantiate a new Observer Instance with options and callback from above */
const myObserver = new IntersectionObserver(myObserverCallback, myObserverOptions);

/* attach the observer to multiple elements */
document.querySelectorAll('.elements').forEach((myObserverInstance) => {
    myObserver.observe(myObserverInstance);
});



/* ###################### the old way ################### */


/* with scroll Event (not good for performance) */
window.addEventListener('scroll', function () {
    var element = document.querySelector('.element');
    var position = element.getBoundingClientRect();

    if (position.top >= 0 && position.bottom <= window.innerHeight) {
        console.log('Element is fully visible in screen');
    }
    if (position.top < window.innerHeight && position.bottom >= 0) {
        console.log('Element is partially visible in screen');
    }
});