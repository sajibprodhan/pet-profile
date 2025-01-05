jQuery(document).ready(function ($) {

    $(document).on('click', '.select_all', function () {
        $('input[name="pet_profiles[]"]').prop('checked', $(this).prop('checked'));
    });

    $(document).on('change', '.bulk-action-selector', function () {
        var action = $(this).val();
        if (action == 'edit') {
            $('.bulk-edit-fields').show();
        } else {
            $('.bulk-edit-fields').hide();
        }
    });


    // Create pet profiles
    $(document).on('submit', '.createPetForm', function (event) {
        event.preventDefault();

        var petCount = $('.pet_insert').val();
        var nonce = ajax_object.nonce;

        if (!petCount || isNaN(petCount) || petCount <= 0) {
            alert('Please enter a valid number of pets greater than 0');
            return;
        }

        if (petCount && petCount > 0) {
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'create_pet_profiles',
                    pet_count: petCount,
                    nonce: nonce
                },
                beforeSend: function () {
                    $('.createPetModal').fadeOut();
                    $('.createNewPetButton').removeClass('createNewPetButton').text('Creating...');
                    $('.spinner-image').show();
                },
                success: function (response) {
                    if (response.success) {
                        $('.addPetButton').text('Create New Pet').addClass('createNewPetButton');
                        $('.spinner-image').hide();
                        $("#bulk-action-form").load(location.href + " #bulk-action-form>*", function () {
                            setTimeout(function () {
                                
                                alert('Pet profiles created successfully!');
                            }, 100);
                        });
                    } else {
                        alert('Failed to create pet profiles.');
                    }
                },
                error: function () {
                    alert('An error occurred while creating pet profiles.');
                }
            });
        } else {
            alert('Please enter a valid number of pets.');
        }
    });



    // Modal js
    $(document).on('click', '.createNewPetButton', function () {
        $(".createPetModal").fadeIn(); // Show modal
    });

    // Click event for Close button
    $(document).on('click', '.closeModal', function () {
        $(".createPetModal").fadeOut(); // Hide modal
    });

    // Close modal if clicking outside modal content
    $(window).on('click', function (event) {
        if ($(event.target).is(".createPetModal")) {
            $(".createPetModal").fadeOut(); 
        }
    });

    $(document).on('change', "input[name='pet_gender']", function () {
        $(".gender-group label").removeClass('active');
        $(this).closest(".gender-group").find("label").addClass('active');
    });


    // Preview image before upload
    $(document).on('change', '#profile', function () {
        const file = this.files[0];
        // Ensure the file is selected and it’s an image
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                // Set the preview image src and display it
                $('#preview-img').attr('src', e.target.result).show();
            };
            // Read the file as a data URL
            reader.readAsDataURL(file);
        } else {
            // If not an image, hide the preview and reset the file input
            $('#preview-img').hide();
            alert('Please select a valid image file.');
            $(this).val(''); // Reset the input value
        }
    
    });


});