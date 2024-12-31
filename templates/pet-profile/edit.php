<div class="wrap">
    <h1><?php _e('Edit Pet Profile', 'pet-profile'); ?></h1>
    
    <form method="post">
        <?php wp_nonce_field( 'update_pet_profile', 'pet_profile_nonce' );?>
        
        <table class="form-table">
            <tr>
                <th><label for="name"><?php _e('Pet Name', 'pet-profile'); ?></label></th>
                <td><input type="text" name="name" id="name" value="<?php echo esc_attr($profile['name']); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="age"><?php _e('Age', 'pet-profile'); ?></label></th>
                <td><input type="number" name="age" id="age" value="<?php echo esc_attr($profile['age']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="gender"><?php _e('Gender', 'pet-profile'); ?></label></th>
                <td>
                    <select name="gender" id="gender">
                        <option value=""><?php _e('Select Gender', 'pet-profile'); ?></option>
                        <option value="male" <?php selected($profile['gender'], 'male'); ?>><?php _e('Male', 'pet-profile'); ?></option>
                        <option value="female" <?php selected($profile['gender'], 'female'); ?>><?php _e('Female', 'pet-profile'); ?></option>
                    </select>
                </td>
            </tr>

            <tr>
                <th><label for="about"><?php _e('About', 'pet-profile'); ?></label></th>
                <td><textarea name="about" id="about" class="regular-text" rows="5"><?php echo esc_textarea( $profile['about'] ?? '' ); ?></textarea></td>
            </tr>

            <tr>
                <th><label for="owner_name"><?php _e('Owner Name', 'pet-profile'); ?></label></th>
                <td><input type="text" name="owner_name" id="owner_name" value="<?php echo esc_attr($profile['owner_name']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="mobile"><?php _e('Mobile', 'pet-profile'); ?></label></th>
                <td><input type="text" name="mobile" id="mobile" value="<?php echo esc_attr($profile['mobile']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="location"><?php _e('Location', 'pet-profile'); ?></label></th>
                <td><input type="text" name="location" id="location" value="<?php echo esc_attr($profile['location']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="facebook"><?php _e('Facebook', 'pet-profile'); ?></label></th>
                <td><input type="url" name="facebook" id="facebook" value="<?php echo esc_attr($profile['facebook']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="whatsapp_id"><?php _e('WhatsApp ID', 'pet-profile'); ?></label></th>
                <td><input type="text" name="whatsapp_id" id="whatsapp_id" value="<?php echo esc_attr($profile['whatsapp_id']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="vaccine_name"><?php _e('Vaccine Name', 'pet-profile'); ?></label></th>
                <td><input type="text" name="vaccine_name" id="vaccine_name" value="<?php echo esc_attr($profile['vaccine_name']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="vaccine_date"><?php _e('Vaccine Date', 'pet-profile'); ?></label></th>
                <td><input type="date" name="vaccine_date" id="vaccine_date" value="<?php echo esc_attr($profile['vaccine_date']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="vaccine_name_2"><?php _e('Vaccine Name 2', 'pet-profile'); ?></label></th>
                <td><input type="text" name="vaccine_name_2" id="vaccine_name_2" value="<?php echo esc_attr($profile['vaccine_name_2']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="vaccine_date_2"><?php _e('Vaccine Date 2', 'pet-profile'); ?></label></th>
                <td><input type="date" name="vaccine_date_2" id="vaccine_date_2" value="<?php echo esc_attr($profile['vaccine_date_2']); ?>" class="regular-text"></td>
            </tr>

            <tr>
                <th><label for="gallery"><?php _e('Gallery', 'pet-profile'); ?></label></th>
                <td><input multiple type="file" name="gallery[]" id="gallery"  class="regular-text"></td>
            </tr>
            
        </table>
        
        <p class="submit">
            <input type="submit" name="update_pet_profile" id="submit" class="button-primary" value="<?php _e('Save Changes', 'pet-profile'); ?>">
            <a href="<?php echo admin_url('admin.php?page=pet_profile'); ?>" class="button"><?php _e('Back to Profiles', 'pet-profile'); ?></a>
        </p>
    </form>
</div>
