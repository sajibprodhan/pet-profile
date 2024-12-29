<?php 
    use Mpdf\QrCode\QrCode;
    use Mpdf\QrCode\Output;
?>

<div class="wrap"> 
    <div>
        <h1 class="wp-heading-inline">Pet Profiles</h1>
        <button type="button" class="page-title-action createNewPetButton addPetButton">Create New Pet</button>
    </div>

    <!-- Search Form -->
    <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" class="search-form">
        <input type="hidden" name="page" value="pet_profile" />
        <input type="text" name="search" value="<?php echo esc_attr( $_GET['search'] ?? '' ); ?>" placeholder="Search by name or location" />
        <input type="submit" value="Search" class="button" />
    </form>

    <!-- Bulk Action Form -->
    <form method="POST" action="" class="bulk-action-form" id="bulk-action-form">
        <div class="spinner-image">
            <!-- Example Spinner -->
            <img src="<?php echo $this->plugin_url . 'assets/images/loading.png'; ?>" alt="Loading..." />
        </div>
        <select name="bulk_action" class="bulk-action-selector">
            <option value="">Bulk Actions</option>
            <option value="download">Download</option>
            <option value="trash">Move to Trash</option>
        </select>
        <input type="submit" value="Apply" class="button action" />

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th class="check-column"><input type="checkbox" class="select_all" /></th>
                    <th>QR-Code</th>
                    <th>Pet Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>About</th>
                    <th>Owner Name</th>
                    <th>Mobile</th>
                    <th>Location</th>
                    <th>Facebook</th>
                    <th>Whatsapp ID</th>
                    <th>Vaccine Name</th>
                    <th>Vaccine Date</th>
                    <th>Vaccine Name 2</th>
                    <th>Vaccine Date 2</th>
                    <th>Gallery</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if ( $results ) {
                            foreach ( $results as $profile ) { 
                                $url = site_url( "/pet-profile-edit/".$profile['id'] );
                                $qrCode = new QrCode( $url );  
                                $output = new Output\Svg();
                            ?>
                            <tr>
                                <td><input type="checkbox" name="pet_profiles[]" value="<?php echo esc_attr( $profile['id'] ); ?>" /></td>
                                <td>
                                    <?php echo $output->output( $qrCode, 64, 'white', 'black' ); ?>
                                </td>
                                <td><?php echo esc_html( $profile['name'] ); ?></td>
                                <td><?php echo esc_html( $profile['age'] ); ?></td>
                                <td><?php echo esc_html( $profile['gender'] ); ?></td>
                                <td><?php echo isset( $profile['about'] ) ? esc_html( wp_trim_words( $profile['about'], 10, '...' ) ) : ''; ?></td>
                                <td><?php echo esc_html( $profile['owner_name'] ); ?></td>
                                <td><?php echo esc_html( $profile['mobile'] ); ?></td>
                                <td><?php echo esc_html( $profile['location'] ); ?></td>
                                <td><a href="<?php echo esc_url( $profile['facebook'] ); ?>" target="_blank"><?php echo esc_html( $profile['facebook'] ); ?></a></td>
                                <td><a href="<?php echo esc_url( $profile['whatsapp_id'] ); ?>" target="_blank"><?php echo esc_html( $profile['whatsapp_id'] ); ?></a></td>
                                <td><?php echo esc_html( $profile['vaccine_name'] ); ?></td>
                                <td><?php echo esc_html( $profile['vaccine_date'] ); ?></td>
                                <td><?php echo esc_html( $profile['vaccine_name_2'] ); ?></td>
                                <td><?php echo esc_html( $profile['vaccine_date_2'] ); ?></td>
                                <td><?php echo $this->get_gallery_column( $profile['gallery'] ); ?></td>
                                <td>
                                    <span class="qrCodeContainers"></span>
                                    <!-- <a href="<?php echo admin_url( 'admin.php?page=edit_pet_profile&id=' . $profile['id'] ); ?>">Edit</a> | -->
                                    <a href="<?php echo admin_url( 'admin.php?page=pet_profile&id=' . $profile['id'] );?>">Edit</a>

                                    <a href="<?php echo admin_url( 'admin-post.php?action=download_pet&id=' . $profile['id'] ); ?>" target="_blank">Download</a>

                                    <!-- <button class="downloadQrCode">Download QR Code</button> -->
                                </td>
                            </tr>
                        <?php
                        }
                    } else {
                        echo '<tr><td colspan="16">No pet profiles found.</td></tr>';
                    }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th class="check-column"><input type="checkbox" class="select_all" /></th>
                    <th>QR-Code</th>
                    <th>Pet Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>About</th>
                    <th>Owner Name</th>
                    <th>Mobile</th>
                    <th>Location</th>
                    <th>Facebook</th>
                    <th>Whatsapp ID</th>
                    <th>Vaccine Name</th>
                    <th>Vaccine Date</th>
                    <th>Vaccine Name 2</th>
                    <th>Vaccine Date 2</th>
                    <th>Gallery</th>
                    <th>Action</th>
                </tr>
            </tfoot>
        </table>
    </form>
</div>


<!-- Modal -->
<div class="createPetModal modal">
    <div class="modal-content">
        <span class="close closeModal">&times;</span>
        <h2>Create New Pet</h2>
        <form class="createPetForm">
            <label for="pet_insert">How many pet to create:</label>
            <input type="number" class="pet_insert" name="pet_insert" required>
            <br>
            <button type="submit">Save Pet</button>
        </form>
    </div>
</div>