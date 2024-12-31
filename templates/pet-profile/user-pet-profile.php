

<?php 
    global $wpdb;
    $table_name = $wpdb->prefix . 'giopio_pet_profile';
    $pet_profile_id = get_query_var( 'pet_profile_id' );
    $query       = $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $pet_profile_id );
    $pet_profile = $wpdb->get_row( $query );
?>

<div>
    <form action="" method="post" enctype="multipart/form-data">
        <!-- Upload Cover Photo start -->
        <div class="w-full header-area">
            <div class="container">
                <div class="text-center upload-area">
                    <input type="file" name="pet_cover_photo" id="cover" class="sr-only">
                    <label for="cover" class="cover">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.25"
                            stroke="currentColor" class="upload-icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                        </svg>
                    </label>
                    <h5 class="header-title">Upload Cover Photo</h5>
                </div>
            </div>
        </div>
        <!-- Upload Cover Photo end -->

        <!-- profile photo start -->
        <div class="w-full px-2 -mt-5">
            <div class="container">
                <div class="text-start profile-area">
                    <input type="file" name="profile_picture" id="profile" class="sr-only">
                    <label for="profile" class="upload-avatar">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="upload-avatar-icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </label>
                </div>
            </div>
        </div>
        <!-- profile photo end -->

        <!-- form inputs -->
        <div class="w-full px-2 my-10">
            <div class="container">
                <div class="form-area">
                    <div class="form-left">
                        <h4>Pet Details</h4>

                        <div class="form-group">
                            <label for="pet-name" class="normal-label">Pet Name</label>
                            <input type="text" placeholder="Enter pet name" id="pet-name" name="pet_name" value="<?php echo $pet_profile->name ?: ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="pet-age" class="normal-label">Pet Age</label>
                            <input type="text" placeholder="Enter pet name" id="pet-age" name="pet_age">
                        </div>
                        <div class="form-group">
                            <label for="pet-age" class="normal-label">Pet Gender</label>
                            <div class="pet-gender">
                                <div class="gender-group">
                                    <label for="male" class="active">M</label>
                                    <input type="radio" name="pet_gender" id="male" value="male" class="sr-only">
                                </div>
                                <div class="gender-group">
                                    <label for="female">F</label>
                                    <input type="radio" name="pet_gender" id="female" value="female" class="sr-only">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pet-age" class="normal-label">Upload Pictures</label>
                            <div class="pictures-group">
                                <!-- box -->
                                <div class="picture-box">
                                    <label for="picture-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </label>
                                    <input type="file" name="pet_gallery[]" id="picture-1" class="sr-only">
                                </div>
                                <!-- box -->
                                <!-- box -->
                                <div class="picture-box">
                                    <label for="picture-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </label>
                                    <input type="file" name="pet_gallery[]" id="picture-2" class="sr-only">
                                </div>
                                <!-- box -->
                                <!-- box -->
                                <div class="picture-box">
                                    <label for="picture-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </label>
                                    <input type="file" name="pet_gallery[]" id="picture-3" class="sr-only">
                                </div>
                                <!-- box -->
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="about" class="normal-label">About Your Pet</label>
                            <textarea rows="5" name="pet_about" id="about"
                                placeholder="Write something about your pet....."><?php echo $pet_profile->about ?: ''; ?></textarea>
                        </div>
                    </div>
                    <div class="form-right">
                        <h4>Owners Details</h4>
                        <div class="custom-width">
                            <div class="form-group">
                                <label for="owner-name" class="normal-label">Owner’s Name</label>
                                <input type="text" placeholder="Enter Name" id="owner-name" name="pet_owner_name" value="<?php echo $pet_profile->owner_name ?: ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="owner-mobile" class="normal-label">Owner’s Mobile</label>
                                <input type="text" placeholder="Owner’s Mobile Number" id="owner-mobile"
                                    name="pet_mobile" value="<?php echo $pet_profile->mobile ?: ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="owner-location" class="normal-label">Owner’s Location</label>
                                <input type="text" placeholder="Owner’s Location" id="owner-location" name="pet_location" value="<?php echo $pet_profile->location ?: ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="fb-id" class="normal-label">Facebook ID</label>
                                <input type="text" placeholder="ID Link" id="fb-id" name="pet_facebook" value="<?php echo $pet_profile->facebook ?: ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="wapp-id" class="normal-label">What’s app link</label>
                                <input type="text" placeholder="ID Link" id="wapp-id" name="pet_whatsapp" value="<?php echo $pet_profile->whatsapp_id ?: ''; ?>">
                            </div>
                        </div>
                        <h4 class="mt-5">Vaccination Status</h4>
                        <div class="grid-layout">
                            <?php
                                $date1 = $pet_profile->vaccine_date ? new DateTime( $pet_profile->vaccine_date ) : null;
                                $date2 = $pet_profile->vaccine_date ? new DateTime( $pet_profile->vaccine_date_2 ) : null;
                            ?>
                            <div class="form-group vaccine-group">
                                <input type="checkbox" name="vaccine_name" id="vaccine_name_status">
                                <input type="text" placeholder="Vaccine Name" id="vaccine_name" name="pet_vaccine_name" value="<?php echo $pet_profile->vaccine_name ?: ''; ?>">
                                <input type="date" placeholder="Date" id="vaccine_date" name="pet_vaccine_date"
                                    class="vaccine-date" value="<?php echo $date1 ? $date1->format( 'd M Y' ) : ''; ?>">
                            </div>
                            <div class="form-group vaccine-group">
                                <input type="checkbox" name="vaccine_name_2" id="vaccine_name_status_2">
                                <input type="text" placeholder="Vaccine Name" id="vaccine_name_2" name="pet_vaccine_name_2" value="<?php echo $pet_profile->vaccine_name_2 ?: ''; ?>">
                                <input type="date" placeholder="Date" id="vaccine_date_2" name="pet_vaccine_date_2"
                                    class="vaccine-date" <?php echo $date2 ? $date2->format( 'd M Y' ) : ''; ?>">
                            </div>
                        </div>

                        <div class="text-center mt-5 submit-bttn">
                            <button type="submit" class="submit-button">Update Profile</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- form inputs -->
    </form>
</div>