<?php
global $wpdb;
$table_name     = $wpdb->prefix . 'giopio_pet_profile';
$pet_profile_id = get_query_var('pet_profile_id');
$query          = $wpdb->prepare("SELECT * FROM $table_name WHERE identifier = %s", $pet_profile_id);
$pet_profile    = $wpdb->get_row($query);
?>

<div>
    <form action="" method="post" enctype="multipart/form-data">
        <!-- Upload Cover Photo start -->
        <div class="w-full header-area <?php echo $pet_profile->cover_photo ? 'preview-header has-cover' : ''; ?>"
            <?php echo $pet_profile->cover_photo ? 'style="background-image: url(' . $pet_profile->cover_photo . ');"' : ''; ?>>
            <div class="container" style="height: 100%;">
                <label for="cover_photo" class="text-center upload-area">
                    <input type="file" name="cover_photo" id="cover_photo" class="sr-only" accept="image/*">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.25"
                            stroke="currentColor" class="upload-icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                        </svg>
                    </div>
                    <h5 class="header-title"><?php echo $pet_profile->cover_photo ? 'Change' : 'Upload'; ?> Cover Photo</h5>
                </label>
            </div>
        </div>
        <!-- Upload Cover Photo end -->

        <!-- profile photo start -->
        <div class="w-full px-2 profile-area-upload">
            <div class="container">
                <div class="text-start profile-area <?php echo $pet_profile->profile_picture ? 'has-profile-pic' : ''; ?>">
                    <input type="file" name="profile_picture" id="profile" class="sr-only" accept="image/*">
                    <label for="profile" class="text-start profile-pic-area profile-shadow">
                        <?php if ($pet_profile->profile_picture): ?>
                            <img id="profilePreview" src="<?php echo $pet_profile->profile_picture; ?>" alt="<?php echo $pet_profile->name; ?>">
                        <?php endif; ?>
                        <div class="upload-iconz">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.25"
                                stroke="currentColor" class="upload-avatar-icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                            </svg>
                        </div>
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
                            <input type="text" placeholder="Enter pet name" id="pet-name" name="pet_name" value="<?php echo $pet_profile->name ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="pet-age" class="normal-label">Pet Age</label>
                            <input type="text" placeholder="Enter pet name" id="pet-age" name="pet_age">
                        </div>
                        <div class="form-group">
                            <label for="pet-age" class="normal-label">Pet Gender</label>
                            <div class="pet-gender">
                                <div class="gender-group">
                                    <label for="male" class="<?php echo isset($pet_profile->gender) && $pet_profile->gender === 'male' ? 'active' : ''; ?>">M</label>
                                    <input
                                        type="radio"
                                        name="pet_gender"
                                        id="male"
                                        value="male"
                                        class="sr-only"
                                        <?php echo isset($pet_profile->gender) ? selected($pet_profile->gender, 'male', false) : ''; ?>>
                                </div>

                                <div class="gender-group">
                                    <label for="female" class="<?php echo isset($pet_profile->gender) && $pet_profile->gender === 'female' ? 'active' : ''; ?>">F</label>
                                    <input
                                        type="radio"
                                        name="pet_gender"
                                        id="female"
                                        value="female"
                                        class="sr-only"
                                        <?php echo isset($pet_profile->gender) ? selected($pet_profile->gender, 'female', false) : ''; ?>>
                                </div>

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="pet-breed" class="normal-label">Pet Breed</label>    
                            <select name="pet_breed" id="">
                                <option value="">Select Breed</option>
                                <option value="dog" <?php echo selected($pet_profile->pet_breed, 'dog', false); ?>>Dog</option>
                                <option value="cat" <?php echo selected($pet_profile->pet_breed, 'cat', false); ?>>Cat</option>
                                <option value="rabbit" <?php echo selected($pet_profile->pet_breed, 'rabbit', false); ?>>Rabbit</option>
                                <option value="others" <?php echo selected($pet_profile->pet_breed, 'others', false); ?> >Others</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="pet-type" class="normal-label">Pet Type</label>    
                            <select name="pet_type" id="">
                                <option value="">Select Type</option>
                                 <option value="breed1" <?php echo selected($pet_profile->pet_type, 'breed1', false); ?>>Breed1</option>
                                <option value="breed2" <?php echo selected($pet_profile->pet_type, 'breed2', false); ?>>Breed1</option>
                                <option value="breed3" <?php echo selected($pet_profile->pet_type, 'breed3', false); ?>>Breed1</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="pet-age" class="normal-label">Upload Pictures</label>
                            <div class="pictures-group">
                                <?php $gallery = isset($pet_profile->gallery) && is_string($pet_profile->gallery) ? explode(',', $pet_profile->gallery) : []; ?>

                                <?php if (!empty($gallery)): ?>
                                    <?php foreach ($gallery as $index => $image_url): ?>
                                        <div class="picture-box">
                                            <label for="picture-<?php echo $index + 1; ?>">
                                                <img src="<?php echo $image_url; ?>" alt="Gallery Image <?php echo $index + 1; ?>" class="uploaded-image">
                                            </label>
                                            <input type="file" name="pet_gallery[]" id="picture-<?php echo $index + 1; ?>" class="sr-only">
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <!-- box for new uploads -->
                                <?php for ($i = count($gallery); $i < 3; $i++): ?>
                                    <div class="picture-box">
                                        <label for="picture-<?php echo $i + 1; ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                        </label>
                                        <input type="file" name="pet_gallery[]" id="picture-<?php echo $i + 1; ?>" class="sr-only">
                                    </div>
                                <?php endfor; ?>
                            </div>

                        </div>
                        <div class="form-group">
                            <label for="about" class="normal-label">About Your Pet</label>
                            <textarea rows="5" name="pet_about" id="about"
                                placeholder="Write something about your pet....."><?php echo $pet_profile->about ?? ''; ?></textarea>
                        </div>
                    </div>
                    <div class="form-right">
                        <h4>Owners Details</h4>
                        <div class="custom-width">
                            <div class="form-group">
                                <label for="owner-name" class="normal-label">Owner’s Name</label>
                                <input type="text" placeholder="Enter Name" id="owner-name" name="pet_owner_name" value="<?php echo $pet_profile->owner_name ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="owner-mobile" class="normal-label">Owner’s Mobile</label>
                                <input type="text" placeholder="Owner’s Mobile Number" id="owner-mobile"
                                    name="pet_mobile" value="<?php echo $pet_profile->mobile ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="owner-location" class="normal-label">Owner’s Location</label>
                                <input type="text" placeholder="Owner’s Location" id="owner-location" name="pet_location" value="<?php echo $pet_profile->location ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="fb-id" class="normal-label">Facebook ID</label>
                                <input type="text" placeholder="ID Link" id="fb-id" name="pet_facebook" value="<?php echo $pet_profile->facebook ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="wapp-id" class="normal-label">What’s app link</label>
                                <input type="text" placeholder="ID Link" id="wapp-id" name="pet_whatsapp" value="<?php echo $pet_profile->whatsapp_id ?? ''; ?>">
                            </div>
                        </div>
                        <h4 class="mt-5">Vaccination Status</h4>
                        <div class="grid-layout">
                            <div class="form-group vaccine-group">
                                <!-- <input type="checkbox" name="vaccine_name" id="vaccine_name_status"> -->
                                <input type="text" placeholder="Vaccine Name" id="vaccine_name" name="pet_vaccine_name" value="<?php echo $pet_profile->vaccine_name ?? ''; ?>">
                                <input type="date" placeholder="Date" id="vaccine_date" name="pet_vaccine_date"
                                    class="vaccine-date" value="<?php echo $pet_profile->vaccine_date ?? ''; ?>">
                                <?php if (isset($_GET['message'])): ?>
                                    <div class="vaccine-message" style="display: none;"><?php echo $_GET['message']; ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group vaccine-group">
                                <!-- <input type="checkbox" name="vaccine_name_2" id="vaccine_name_status_2"> -->
                                <input type="text" placeholder="Vaccine Name" id="vaccine_name_2" name="pet_vaccine_name_2" value="<?php echo $pet_profile->vaccine_name_2 ?? ''; ?>">
                                <input type="date" placeholder="Date" id="vaccine_date_2" name="pet_vaccine_date_2"
                                    class="vaccine-date" value="<?php echo $pet_profile->vaccine_date_2 ?? ''; ?>">
                                <?php if (isset($_GET['messagetwo'])): ?>
                                    <div class="vaccine-message" style="display: none;"><?php echo $_GET['messagetwo']; ?></div>
                                <?php endif; ?>
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