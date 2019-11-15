<?php require 'php/header.php'; ?>

		<h1>Boutique</h1> 
<br/>
<h2>Nos meilleures ventes : </h2>
<style>
* {box-sizing: border-box}
.mySlides {display: none}
img {vertical-align: middle;}

/* Slideshow container */
.slideshow-container {
  max-width: 25%;
  position: relative;
  margin: auto;
}

/* Next & previous buttons */
.prev, .next {
  cursor: pointer;
  position: absolute;
  top: 50%;
  width: auto;
  padding: 16px;
  margin-top: -22px;
  color: white;
  font-weight: bold;
  font-size: 18px;
  transition: 0.6s ease;
  border-radius: 0 3px 3px 0;
  user-select: none;
}

/* Position the "next button" to the right */
.next {
  right: 0;
  border-radius: 3px 0 0 3px;
}

/* On hover, add a black background color with a little bit see-through */
.prev:hover, .next:hover {
  background-color: rgba(0,0,0,0.8);
}

/* Caption text */
.text {
  color: #ffffff;
  background-color:#4d75bb;
  padding: 8px 12px;
  position: absolute;
  bottom: 8px;
  width: 100%;
  text-align: center;
}

/* The dots/bullets/indicators */
.dot {
  cursor: pointer;
  height: 15px;
  width: 15px;
  margin: 0 2px;
  background-color: #bbb;
  border-radius: 50%;
  display: inline-block;
  transition: background-color 0.6s ease;
}

.active, .dot:hover {
  background-color: #717171;
}

@-webkit-keyframes fade {
  from {opacity: .4} 
  to {opacity: 1}
}

@keyframes fade {
  from {opacity: .4} 
  to {opacity: 1}
}

/* On smaller screens, decrease text size */
@media only screen and (max-width: 300px) {
  .prev, .next,.text {font-size: 11px}
}
</style>

<div class="slideshow-container">

<div class="mySlides">
  <img src="https://cadoetik.ch/267161/kit-crayon-papier-et-marque-page-10-cm-inco.jpg" style="width:100%">
  <div class="text">Caption Text</div>
</div>

<div class="mySlides">
  <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT43SK3N2SLA8aAGwJXThZDYQLxKhGSdlK5n2aUvZov-yJOUV8d&s" style="width:100%">
  <div class="text">Caption Two</div>
</div>

<div class="mySlides">
  <img src="data:image/jpeg;base64,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" style="width:100%">
  <div class="text">Caption Three</div>
</div>

<a class="prev" onclick="plusSlides(-1)">&#10094;</a>
<a class="next" onclick="plusSlides(1)">&#10095;</a>

</div>
<br>

<div style="text-align:center">
  <span class="dot" onclick="currentSlide(1)"></span> 
  <span class="dot" onclick="currentSlide(2)"></span> 
  <span class="dot" onclick="currentSlide(3)"></span> 
</div>

<div class="item">
	<h4>Noms items</h4>
	<img/>
	<p>description de l'item que vous voulez acheter</p>
</div>
<div class="item">
	<h4>Nom item</h4>
	<img/>
	<p>description de l'item que vous voulez acheter</p>
</div>
<div class="item">
	<h4>Nom item</h4>
	<img/>
	<p>description de l'item que vous voulez acheter</p>
</div>
<div class="item">
	<h4>Nom item</h4>
	<img src="images/IMG_0888.jpg"/>
	<p>description de l'item que vous voulez acheter</p>
</div>

<script>
var slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  var slides = document.getElementsByClassName("mySlides");
  var dots = document.getElementsByClassName("dot");
  if (n > slides.length) {slideIndex = 1}    
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";  
  }
  for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";  
  dots[slideIndex-1].className += " active";
}
</script>

</body>

</html>
