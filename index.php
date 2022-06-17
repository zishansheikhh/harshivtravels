<?php
    include_once("include-files/autoload-server-files.php");

    $PageTitle = "Homepage";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <META http-equiv="content-type" content="text/html; charset=utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <META name="robots" content="index,follow" />
    <TITLE><?=$PageTitle.' | '._WebsiteName?></TITLE>
    <META name="description" content="description of the webpage" />
    <LINK rel="canonical" href="<?=_HOST?>/"> <!-- Define Canonical !-->

    <?php include_once("include-files/common-css.php"); ?>
    <?php include_once("include-files/common-js.php"); ?>

    <script>
        $(document).ready(function(){
            // Owl Carousal
            $("#owl-slider").owlCarousel({
                items: 1,
                nav: false,
                dots: true,
                loop: true,
                autoplay: true,
                autoplayTimeout:5000,
                responsive: {
                    0: {
                    items: 1
                    }
                }
            });
        });
    </script>
</head>

<body>
    
    <?php include_once("include-files/header.php"); ?>
    
    <div class="container">
        <section class="travel-search-icons">
            <div class="row">
                <div class="col s3">
                    <a href=""><i class="fa-solid fa-route"></i></a>
                    <p>One Way</p>
                </div>
                <div class="col s3">
                    <a href=""><i class="fa-solid fa-circle-notch"></i></a>
                    <p>Round Trip</p>
                </div>
                <div class="col s3">
                    <a href=""><i class="fa-solid fa-street-view"></i></a>
                    <p>Local</p>
                </div>
                <div class="col s3">
                    <a href=""><i class="fa-solid fa-truck-fast"></i></a>
                    <p>Transfer</p>
                </div>
            </div>
        </section>

        <section class="travel-search">
            <form class="travel-search-form">
                <section>
                    <label for="autocomplete-input">Pick-up City Name</label>
                    <input type="text" id="autocomplete-input" class="autocomplete">
                </section>
                <section>
                    <label>Drop-off City Name</label>
                    <input type="text">
                </section>
                <button class="submit-btn">Submit<i class="fa-solid fa-angles-right"></i></button>
            </form>
        </section>

        <section class="contact-us">
            <form class="contact-form" action="/ajax/send-enquiry/" id="sendContactUs">
                <div class="default-header">
                    <h3>Quick Query</h3>
                    <div class="header-underline"></div>
                </div>
                <input type="hidden" name="option" value="enquiry_form">
                <section>
                    <label>Your Name*</label>
                    <input class="form-control" type="text" maxlength="50" name="FullName">
                </section>
                <section>
                    <label>E-mail*:</label>
                    <input class="form-control" type="text" maxlength="50" name="Email">
                </section>
                <section>
                    <label>Phone*:</label>
                    <input class="form-control" type="tel" maxlength="15" name="Phone">
                </section>
                <section>
                    <label>Query*</label>
                    <input class="form-control" type="text" maxlength="150" name="Message">
                </section>
                <button type="button" class="submit-btn">Send Message<i class="fa-solid fa-angles-right"></i></button>
            </form>
        </section>

        <section class="usp-icons">
            <div class="row">
                <div class="col l4 m4 s12">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <p>100+ Destinations</p>
                </div>
                <div class="col l4 m4 s12">
                    <i class="fa-solid fa-signs-post"></i>
                    <p>Major Cities Covered</p>
                </div>
                <div class="col l4 m4 s12">
                    <i class="fa-solid fa-headset"></i>
                    <p>24x7 Customer Support</p>
                </div>
            </div>
        </section>

        <section class="booking-vehicle">
            <div class="default-header">
                <h3>Vehicle Details</h3>
                <div class="header-underline"></div>
            </div>
            <div class="row">
                <div class="col l4 m4 s12">
                    <div class="card">
                        <div class="card-image">
                            <img src = "/images/car-1.png">
                        </div>
                        <div class="card-content">
                            <div class="card-icons">
                                <img src="/images/ico/seat.svg">
                                <img src="/images/ico/stick.svg">
                                <img src="/images/ico/gas-pump.svg">
                            </div>
                            <a href="/" class="submit-btn">Book Now</a>
                        </div>
                    </div>
                </div>

                <div class="col l4 m4 s12">    
                    <div class="card">
                        <div class="card-image">
                            <img src = "/images/car-2.png">
                        </div>
                        <div class="card-content">
                            <div class="card-icons">
                                <img src="/images/ico/seat.svg">
                                <img src="/images/ico/stick.svg">
                                <img src="/images/ico/gas-pump.svg">
                            </div>
                            <a href="/" class="submit-btn">Book Now</a>
                        </div>
                    </div>
                </div>

                <div class="col l4 m4 s12">
                    <div class="card">
                        <div class="card-image">
                            <img src = "/images/car-1.png">
                        </div>
                        <div class="card-content">
                            <div class="card-icons">
                                <img src="/images/ico/seat.svg">
                                <img src="/images/ico/stick.svg">
                                <img src="/images/ico/gas-pump.svg">
                            </div>
                            <a href="/" class="submit-btn">Book Now</a>
                        </div>
                    </div>
                </div>

                <div class="col l4 m4 s12">
                    <div class="card">
                        <div class="card-image">
                            <img src = "/images/car-2.png">
                        </div>
                        <div class="card-content">
                            <div class="card-icons">
                                <img src="/images/ico/seat.svg">
                                <img src="/images/ico/stick.svg">
                                <img src="/images/ico/gas-pump.svg">
                            </div>
                            <a href="/" class="submit-btn">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="user-testimonials">
            <div class="default-header">
                <h3>What People Say</h3>
                <div class="header-underline"></div>
            </div>
            <!-- Owl Carousal -->
            <div class="card">
            <div class="owl-wrapper">
            <div class="owl-slider" id="owl-slider">
                <!-- slide 01 -->
                <div class="slide">
                    <div class="owl-slide">
                        <img src="/images/65.jpg">
                        <div class="owl-text">
                            <span>"The staff and service was great. Everything was smooth and easy. They also guided me so well. Thankyou for your services".</span>  
                        </div>
                    </div>
                </div>
                <!-- slide 02 -->
                <div class="slide">
                    <div class="owl-slide">
                        <img src="/images/79.jpg">
                        <div class="owl-text">
                            <span>"Harshiv Travels offers a brilliant service. Affordable, quick and easy to navigate that gives me the confidence and belief that this legal necessity has been done right!"</span>  
                        </div>
                    </div>
                </div>
                <!-- slide 03 -->
                <div class="slide">
                    <div class="owl-slide">
                        <img src="/images/8.jpg">
                        <div class="owl-text">
                            <span>"This was an amazingly easy experience. Thank you, worth the money I paid for a premium professional"</span>
                        </div>
                    </div>
                </div>
            </div>
            </div></div>
            <!-- Carousel Ends -->
        </section>
    </div>

    <?php include_once("include-files/footer.php"); ?>
    
    </body>
</html>
