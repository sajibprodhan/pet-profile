∆<div>

    <?php if (isset($_GET['action']) && $_GET['action'] == 'edit') : ?>
        <?php include $this->plugin_path . 'templates/pet-profile/user-pet-profile.php'; ?>
    <?php else: ?>

        <?php if ($pet_profile->cover_photo) : ?>
            <div class="w-full preview-header">
                <a class="example-image-link" href="<?php echo $pet_profile->cover_photo; ?>" data-lightbox="mobile-banner">
                    <img src="<?php echo $pet_profile->cover_photo; ?>" alt="cat banner" class="banner-img mobile-banner">
                </a>
                <a class="example-image-link" href="<?php echo $pet_profile->cover_photo; ?>" data-lightbox="desktop-banner">
                    <img src="<?php echo $pet_profile->cover_photo; ?>" alt="cat banner" class="banner-img desktop-banner">
                </a>
            </div>
        <?php endif; ?>
        <!-- Upload Cover Photo end -->

        <!-- profile photo start -->
        <?php if ($pet_profile->profile_picture) : ?>
            <div class="w-full px-2 -mt-5 mt-0">
                <div class="container">
                    <div class="text-start profile-pic-area">
                        <a class="example-image-link" style="display: block; width: 100%; height: 100%" href="<?php echo $pet_profile->profile_picture; ?>" data-lightbox="Profile-pic">
                            <img src="<?php echo $pet_profile->profile_picture; ?>" alt="Profile-pic">
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <!-- profile photo end -->

        <!-- form inputs -->
        <div class="w-full px-2 mt-2 preview-section">
            <div class="container">
                <div class="form-area">
                    <div class="form-left">
                        <div class="name-flex">
                            <h3 class="pet-title-name">Whisker</h3>
                            <a href="<?php echo site_url("pet-profile/" . $pet_profile->id); ?>">Edit Proofile</a>
                        </div>
                        <?php if ($pet_profile->about) : ?>
                            <div class="about-pet">
                                <p>
                                    <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 57.26 51.21">
                                        <defs>
                                            <style>
                                                .cls-1 {
                                                    fill: #737373;
                                                }
                                            </style>
                                        </defs>
                                        <g id="MdPMe6">
                                            <path class="cls-1"
                                                d="M30.05,55.61a19.36,19.36,0,0,1-2.92-1.35Q15.29,46.13,3.52,37.91a4.18,4.18,0,0,1-1.9-5.2Q5,20,8.38,7.26c.41-1.57,1.3-2.72,2.89-2.81s2.58,1,3.13,2.52c1.53,4.18,3.15,8.32,4.66,12.5a1.73,1.73,0,0,0,2,1.3q9-.07,18.05,0a1.62,1.62,0,0,0,1.83-1.24c1.49-4.07,3.06-8.12,4.56-12.18.69-1.87,1.89-3,3.2-2.95s2.46,1.15,3,3.13q3.29,12.32,6.6,24.63c.81,3,.41,4.23-2.12,6Q44.55,46.19,32.94,54.21A21.6,21.6,0,0,1,30.05,55.61ZM11.72,10.82c-.35.42-.45.5-.48.59-2,7.34-4,14.68-5.92,22.05a2.07,2.07,0,0,0,.8,1.66c7.48,5.24,15,10.41,22.48,15.65a2.07,2.07,0,0,0,2.8,0Q42.62,42.9,53.93,35.17a1.58,1.58,0,0,0,.71-2.07C52.8,26.41,51,19.69,49.22,13c-.18-.65-.42-1.29-.62-1.93a3.85,3.85,0,0,0-1.18,1.77c-1.26,3.33-2.54,6.65-3.74,10a2.3,2.3,0,0,1-2.49,1.73q-11.2,0-22.4,0a2.3,2.3,0,0,1-2.48-1.74c-1.2-3.36-2.48-6.68-3.73-10C12.35,12.21,12.07,11.64,11.72,10.82Z"
                                                transform="translate(-1.37 -4.39)" />
                                        </g>
                                    </svg>
                                    About Pet :
                                </p>
                                <div>
                                    <?php echo $pet_profile->about; ?>
                                </div>
                            <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($pet_profile->pet_breed) : ?>
                            <div class="form-group">
                                <label for="pet-breed" class="normal-label">Pet Breed</label>    
                                <select id="">
                                    <option value="">Select Breed</option>
                                    <option value="dog" <?php echo selected($pet_profile->pet_breed, 'dog', false); ?>>Dog</option>
                                    <option value="cat" <?php echo selected($pet_profile->pet_breed, 'cat', false); ?>>Cat</option>
                                    <option value="rabbit" <?php echo selected($pet_profile->pet_breed, 'rabbit', false); ?>>Rabbit</option>
                                    <option value="others" <?php echo selected($pet_profile->pet_breed, 'others', false); ?> >Others</option>
                                </select>
                            </div>
                        <?php endif; ?>

                        <?php if ($pet_profile->pet_type) : ?>
                            <div class="form-group">
                                <label for="pet-type" class="normal-label">Pet Type</label>    
                                <select>
                                    <option value="">Select Type</option>
                                    <option value="breed1" <?php echo selected($pet_profile->pet_type, 'breed1', false); ?>>Breed1</option>
                                    <option value="breed2" <?php echo selected($pet_profile->pet_type, 'breed2', false); ?>>Breed1</option>
                                    <option value="breed3" <?php echo selected($pet_profile->pet_type, 'breed3', false); ?>>Breed1</option>
                                </select>
                            </div>
                         <?php endif; ?>

                        <?php if ($pet_profile->gallery) : ?>
                            <div class="form-group">
                                <div class="pet-gender">
                                    <?php if ($pet_profile->gender) : ?>
                                        <div class="gender-group">
                                            <label for="male" class="active"><?php echo ucfirst($pet_profile->gender); ?></label>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($pet_profile->age) : ?>
                                        <div class="gender-group">
                                            <label for="male" class="active"><?php echo $pet_profile->age; ?> Years</label>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ($pet_profile->gallery) : ?>
                                <div class="form-group">
                                    <label for="pet-age" class="normal-label">Pictures</label>
                                    <div class="pictures-group">
                                        <!-- box -->
                                        <?php
                                        $gallery = explode(",", $pet_profile->gallery);
                                        foreach ($gallery as $picture) {
                                            $picture = trim($picture);
                                        ?>
                                            <div class="picture-box">
                                                <img src="<?php echo esc_url($picture); ?>" alt="Pet Picture">
                                            </div>
                                        <?php
                                        }
                                        ?>

                                        <!-- <div class="picture-box">
                                        <label for="picture-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                        </label>
                                        <input type="file" name="picture-3" id="picture-3" class="sr-only">
                                    </div> -->
                                        <!-- box -->
                                    </div>
                                </div>
                            <?php endif; ?>
                    </div>
                    <div class="preview-right">
                        <div class="name-box">
                            <?php if ($pet_profile->owner_name) : ?>
                                <div class="left">
                                    <h5><?php echo ucfirst($pet_profile->owner_name); ?></h5>
                                    <p>Pet Owner</p>
                                </div>
                            <?php endif; ?>
                            <div class="left">
                                <ul>
                                    <?php if ($pet_profile->facebook) : ?>
                                        <li>
                                            <a target="_blank" href="<?php echo esc_url($pet_profile->facebook); ?>">
                                                <img src="<?php echo $this->plugin_url; ?>assets/images/facebook.png" alt="icon">
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if ($pet_profile->whatsapp_id) : ?>
                                        <li>
                                            <a target="_blank" href="<?php echo esc_url($pet_profile->whatsapp_id); ?>">
                                                <img src="<?php echo $this->plugin_url; ?>assets/images/call-icon.png" alt="icon">
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>

                        <?php if ($pet_profile->location || $pet_profile->mobile) : ?>
                            <h4>Owners Details</h4>

                            <div class="adress-box">
                                <?php if ($pet_profile->location) : ?>
                                    <div class="text-start">
                                        <h6>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                            </svg>

                                            Location
                                        </h6>
                                        <p><?php echo $pet_profile->location; ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php if ($pet_profile->mobile) : ?>
                                    <div class="text-start">
                                        <h6>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 3.75v4.5m0-4.5h-4.5m4.5 0-6 6m3 12c-8.284 0-15-6.716-15-15V4.5A2.25 2.25 0 0 1 4.5 2.25h1.372c.516 0 .966.351 1.091.852l1.106 4.423c.11.44-.054.902-.417 1.173l-1.293.97a1.062 1.062 0 0 0-.38 1.21 12.035 12.035 0 0 0 7.143 7.143c.441.162.928-.004 1.21-.38l.97-1.293a1.125 1.125 0 0 1 1.173-.417l4.423 1.106c.5.125.852.575.852 1.091V19.5a2.25 2.25 0 0 1-2.25 2.25h-2.25Z" />
                                            </svg>
                                            Mobile
                                        </h6>
                                        <p><?php echo $pet_profile->mobile; ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>


                        <?php if ($pet_profile->vaccine_date || $pet_profile->vaccine_name || $pet_profile->vaccine_date_2 || $pet_profile->vaccine_name_2) : ?>
                            <h4>Vaccination Status</h4>

                            <div class="vaccine-status">
                                <?php if ($pet_profile->vaccine_name && $pet_profile->vaccine_date) : ?>
                                    <h5>
                                        <svg class="vaccine-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="3" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>

                                        <?php echo $pet_profile->vaccine_name; ?>
                                        <span>
                                            <?php
                                            $date = new DateTime($pet_profile->vaccine_date);
                                            echo $date->format('d M Y');
                                            ?>
                                        </span>
                                    </h5>
                                <?php endif; ?>

                                <?php if ($pet_profile->vaccine_name_2 && $pet_profile->vaccine_date_2) : ?>
                                    <h5>
                                        <svg class="vaccine-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>

                                        <?php echo $pet_profile->vaccine_name_2; ?>
                                        <span>
                                            <?php
                                            $date = new DateTime($pet_profile->vaccine_date_2);
                                            echo $date->format('d M Y');
                                            ?>
                                        </span>
                                    </h5>
                                <?php endif; ?>
                            </div>

                        <?php endif; ?>



                        <?php if ($pet_profile->user_id == get_current_user_id()) : ?>
                            <div class="edit-pro-bttn">
                                <a href="<?php echo site_url("pet-profile/" . $pet_profile->identifier . "/?action=edit"); ?>">Edit Proofile</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- form inputs -->
    <?php endif; ?>
</div>