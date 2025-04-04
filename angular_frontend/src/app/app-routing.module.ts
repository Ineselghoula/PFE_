import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SearchBarComponent } from './search-bar/search-bar.component'; // Importez SearchBarComponent
import { RegisterComponent } from './register/register.component';
import { VerifyEmailComponent } from './verify-email/verify-email.component';
import { LoginComponent } from './login/login.component';
import { HomeComponent } from './home/home.component';
import { ResendCodeComponent } from './resendcode/resendcode.component';
import { ProfileComponent } from './profile/profile.component';

const routes: Routes = [
  { path: '', component: HomeComponent },  // Utilise un autre composant comme page d'accueil
  { path: 'search', component: SearchBarComponent },
  { path: 'register', component: RegisterComponent },
  { path: 'verify-email', component: VerifyEmailComponent },
  { path: 'login', component: LoginComponent },
  { path: 'resend-code', component: ResendCodeComponent },
  { path: 'profile', component: ProfileComponent },  
  { path: '**', redirectTo: '/', pathMatch: 'full' }, 
];



@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }