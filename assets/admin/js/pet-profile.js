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






    // Preview cover photo before upload
    const $coverPhotoInput = $('#cover_photo');
    const $headerArea = $('.header-area');

    $coverPhotoInput.on('change', function (event) {
        const file = event.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $headerArea.css('background-image', `url('${e.target.result}')`);
                $headerArea.addClass('preview-header has-cover');
            };

            reader.readAsDataURL(file);
        } else {
            alert('Please upload a valid image file.');
        }
    });




    // Preview profile image before upload
    const $profileInput = $('#profile');
    const $profileLabel = $('.profile-pic-area');
    let $profilePreview = $('#profilePreview');

    $profileInput.on('change', function (event) {
        const file = event.target.files[0];

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();

            reader.onload = function (e) {
                if (!$profilePreview.length) {
                    $profilePreview = $('<img>', {
                        id: 'profilePreview',
                        alt: 'Uploaded Profile Picture'
                    });

                    const $uploadIcon = $profileLabel.find('.upload-avatar-icon').parent();
                    $profileLabel.prepend($profilePreview);
                }

                $profilePreview.attr('src', e.target.result);

                const $uploadIcon = $profileLabel.find('.upload-avatar-icon');
                if ($uploadIcon.length) {
                    $uploadIcon.show();
                }
            };

            reader.readAsDataURL(file);
        } else {
            alert('Please upload a valid image file.');
            if ($profilePreview.length) {
                $profilePreview.remove();
                $profilePreview = $();
            }
        }
    });

});

// cover photo upload preview
// document.addEventListener('DOMContentLoaded', function () {
//     const coverPhotoInput = document.getElementById('cover_photo');
//     const headerArea = document.querySelector('.header-area');

//     coverPhotoInput.addEventListener('change', function (event) {
//         const file = event.target.files[0];
//         if (file && file.type.startsWith('image/')) {
//             const reader = new FileReader();
//             reader.onload = function (e) {
//                 headerArea.style.backgroundImage = `url('${e.target.result}')`;
//                 headerArea.classList.add('preview-header', 'has-cover');
//             };
//             reader.readAsDataURL(file);
//         } else {
//             alert('Please upload a valid image file.');
//         }
//     });
// });

// upload profile picture
// document.addEventListener('DOMContentLoaded', function () {
//     const profileInput = document.getElementById('profile');
//     const profileLabel = document.querySelector('.profile-pic-area');
//     let profilePreview = document.getElementById('profilePreview');

//     profileInput.addEventListener('change', function (event) {
//         const file = event.target.files[0];

//         if (file && file.type.startsWith('image/')) {
//             const reader = new FileReader();

//             reader.onload = function (e) {
//                 if (!profilePreview) {
//                     const viewProfilePictureDiv = document.createElement('div');
//                     viewProfilePictureDiv.classList.add('view-profile-picture');

//                     profilePreview = document.createElement('img');
//                     profilePreview.id = 'profilePreview';
//                     profilePreview.alt = 'Uploaded Profile Picture';

//                     const uploadIcon = profileLabel.querySelector('.upload-avatar-icon').parentElement;
//                     profileLabel.insertBefore(profilePreview, uploadIcon);
//                 }

//                 profilePreview.src = e.target.result;

//                 const uploadIcon = profileLabel.querySelector('.upload-avatar-icon');
//                 if (uploadIcon) {
//                     uploadIcon.style.display = 'block';
//                 }
//             };

//             reader.readAsDataURL(file);
//         } else {
//             alert('Please upload a valid image file.');
//             if (profilePreview) {
//                 profilePreview.remove();
//                 profilePreview = null;
//             }
//         }
//     });
// });
