# TODO: Implement Profile Picture Saving to Database

## Plan Steps (Approved by user)

1. **[x]** Create directory `public/uploads/profiles` in backend.
2. **[x]** Update UserModel.php: Add 'profile_pic' to allowedFields.
3. **[x]** Update Migration CreateUsersTable.php: Add profile_pic column (idempotent ALTER).
4. **[x]** Update AuthController.php: 
   - Fix register(): Remove misplaced upload, make optional if needed.
   - Add updateProfile() method: Handle PUT /profile, file upload, update user.
5. **[x]** Add route to Routes.php: `$routes->put('profile', 'AuthController::updateProfile');`
6. **[x]** Update Editprofileview.vue: saveProfile() → FormData POST/PUT to backend API, update localStorage from response.
7. **[x]** Update ProfileView.vue: Support profile_pic_url in avatarUrl.
8. **[x]** Run `php spark migrate` and test locally.
9. **[x]** Deploy and verify on Render/Vercel.

**Task complete!**

