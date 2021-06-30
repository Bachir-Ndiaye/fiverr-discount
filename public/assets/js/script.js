// Get all images and texts, get the `canvas` element, and save slider length
var sliderCanvas = document.querySelector(".pieces-slider__canvas");
var imagesEl = [].slice.call(
  document.querySelectorAll(".pieces-slider__image")
);
var textEl = [].slice.call(document.querySelectorAll(".pieces-slider__text"));
var slidesLength = imagesEl.length;

// Define indexes related variables, as we will use indexes to reference items
var currentIndex = 0,
  currentImageIndex,
  currentTextIndex,
  currentNumberIndex;
var textIndexes = [];
var numberIndexes = [];

// Update current indexes for image, text and number
function updateIndexes() {
  currentImageIndex = currentIndex * 3;
  currentTextIndex = currentImageIndex + 1;
  currentNumberIndex = currentImageIndex + 2;
}
updateIndexes();

// Options for images
var imageOptions = {
  angle: 45, // rotate item pieces 45deg
  extraSpacing: { extraX: 100, extraY: 200 }, // this extra spacing is needed to cover all the item, because angle != 0
  piecesWidth: function () {
    return Pieces.random(50, 200);
  }, // every piece will have a random width between 50px and 200px
  ty: function () {
    return Pieces.random(-400, 400);
  }, // every piece will be translated in the Y axis a random distance between -400px and 400px
};

// Options for texts
var textOptions = {
  color: "white",
  backgroundColor: "#0066CC",
  fontSize: function () {
    return windowWidth > 720 ? 50 : 30;
  },
  padding: "15 20 10 20",
  angle: -45,
  extraSpacing: { extraX: 0, extraY: 300 },
  piecesWidth: function () {
    return Pieces.random(50, 200);
  },
  ty: function () {
    return Pieces.random(-200, 200);
  },
  translate: function () {
    if (windowWidth > 1120) return { translateX: 200, translateY: 200 };
    if (windowWidth > 720) return { translateX: 0, translateY: 200 };
    return { translateX: 0, translateY: 100 };
  },
};

// Options for numbers
var numberOptions = {
  color: "white",
  backgroundColor: "#0066CC",
  backgroundRadius: 300,
  fontSize: function () {
    return windowWidth > 720 ? 100 : 50;
  },
  padding: function () {
    return windowWidth > 720 ? "18 35 10 38" : "18 25 10 28";
  },
  angle: 0,
  piecesSpacing: 2,
  extraSpacing: { extraX: 10, extraY: 10 },
  piecesWidth: 35,
  ty: function () {
    return Pieces.random(-200, 200);
  },
  translate: function () {
    if (windowWidth > 1120) return { translateX: -340, translateY: -180 };
    if (windowWidth > 720) return { translateX: -240, translateY: -180 };
    return { translateX: -140, translateY: -100 };
  },
};

// Build the array of items to draw using Pieces
var items = [];
var imagesReady = 0;
for (var i = 0; i < slidesLength; i++) {
  // Wait for all images to load before initializing the slider and event listeners
  var slideImage = new Image();
  slideImage.onload = function () {
    if (++imagesReady == slidesLength) {
      initSlider();
      initEvents();
    }
  };
  // Push all elements for each slide with the corresponding options
  items.push({ type: "image", value: imagesEl[i], options: imageOptions });
  items.push({
    type: "text",
    value: textEl[i].innerText,
    options: textOptions,
  });
  items.push({ type: "text", value: i + 1, options: numberOptions });
  // Save indexes
  textIndexes.push(i * 3 + 1);
  numberIndexes.push(i * 3 + 2);
  // Set image src
  slideImage.src = imagesEl[i].src;
}

// Save the new Pieces instance
piecesSlider = new Pieces({
  canvas: sliderCanvas, // CSS selector to get the canvas
  items: items, // the Array of items we've built before
  x: "centerAll", // center all items in the X axis
  y: "centerAll", // center all items in the Y axis
  piecesSpacing: 1, // default spacing between pieces
  fontFamily: ["'Helvetica Neue', sans-serif"],
  animation: {
    // animation options to use in any operation
    duration: function () {
      return Pieces.random(1000, 2000);
    },
    easing: "easeOutQuint",
  },
  debug: false, // set `debug: true` to enable debug mode
});

// Animate all numbers to rotate clockwise indefinitely
piecesSlider.animateItems({
  items: numberIndexes,
  duration: 20000,
  angle: 360,
  loop: true,
});

// Show current items: image, text and number
showItems();

// Show current items: image, text and number
function showItems() {
  // Show image pieces
  piecesSlider.showPieces({
    items: currentImageIndex,
    ignore: ["tx"],
    singly: true,
    update: (anim) => {
      // Stop the pieces animation at 60%, and run a new indefinitely animation of `ty` for each piece
      if (anim.progress > 60) {
        var piece = anim.animatables[0].target;
        var ty = piece.ty;
        anime.remove(piece);
        anime({
          targets: piece,
          ty:
            piece.h_ty < 300
              ? [
                  { value: ty + 10, duration: 1000 },
                  { value: ty - 10, duration: 2000 },
                  { value: ty, duration: 1000 },
                ]
              : [
                  { value: ty - 10, duration: 1000 },
                  { value: ty + 10, duration: 2000 },
                  { value: ty, duration: 1000 },
                ],
          duration: 2000,
          easing: "linear",
          loop: true,
        });
      }
    },
  });
  // Show pieces for text and number, using alternate `ty` values
  piecesSlider.showPieces({ items: currentTextIndex });
  piecesSlider.showPieces({
    items: currentNumberIndex,
    ty: function (p, i) {
      return p.s_ty - [-3, 3][i % 2];
    },
  });
}

// Hide current items: image, text and number
function hideItems() {
  piecesSlider.hidePieces({
    items: [currentImageIndex, currentTextIndex, currentNumberIndex],
  });
}

// Select the prev slide: hide current items, update indexes, and show the new current item
function prevItem() {
  hideItems();
  currentIndex = currentIndex > 0 ? currentIndex - 1 : slidesLength - 1;
  updateIndexes();
  showItems();
}

// Select the next slide: hide current items, update indexes, and show the new current item
function nextItem() {
  hideItems();
  currentIndex = currentIndex < slidesLength - 1 ? currentIndex + 1 : 0;
  updateIndexes();
  showItems();
}

// Select prev or next slide using buttons
prevButtonEl.addEventListener("click", prevSlide);
nextButtonEl.addEventListener("click", nextSlide);

// Select prev or next slide using arrow keys
document.addEventListener("keydown", function (e) {
  if (e.keyCode == 37) {
    // left
    prevSlide();
  } else if (e.keyCode == 39) {
    // right
    nextSlide();
  }
});

// Handle `resize` event
window.addEventListener("resize", resizeStart);

var initial = true,
  hideTimer,
  resizeTimer;

// User starts resizing, so wait 300 ms before reinitialize the slider
function resizeStart() {
  if (initial) {
    initial = false;
    if (hideTimer) clearTimeout(hideTimer);
    sliderCanvas.classList.add("pieces-slider__canvas--hidden");
  }
  if (resizeTimer) clearTimeout(resizeTimer);
  resizeTimer = setTimeout(resizeEnd, 300);
}

// User ends resizing, then reinitialize the slider
function resizeEnd() {
  initial = true;
  windowWidth = window.innerWidth;
  initSlider();
  hideTimer = setTimeout(() => {
    sliderCanvas.classList.remove("pieces-slider__canvas--hidden");
  }, 500);
}
