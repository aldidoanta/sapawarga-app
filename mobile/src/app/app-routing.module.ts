import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AuthGuard } from './guards/auth.guard';

const routes: Routes = [
  { path: 'login', loadChildren: './pages/login/login.module#LoginPageModule' },
  { path: '', loadChildren: './tabs/tabs.module#TabsPageModule' },
  {
    path: 'register',
    loadChildren: './pages/register/register.module#RegisterPageModule'
  },
  { path: 'about', loadChildren: './pages/about/about.module#AboutPageModule' },
  {
    path: 'settings',
    loadChildren: './pages/settings/settings.module#SettingsPageModule'
  },
  {
    path: 'edit-profile',
    loadChildren:
      './pages/edit-profile/edit-profile.module#EditProfilePageModule'
  },
  {
    path: 'home-results',
    // canActivate: [AuthGuard],
    loadChildren:
      './pages/home-results/home-results.module#HomeResultsPageModule'
  },
  {
    path: 'view-profile',
    loadChildren:
      './pages/view-profile/view-profile.module#ViewProfilePageModule'
  }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {}