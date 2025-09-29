import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MerchItem, CartItem, ShopService } from 'src/app/services/shop.service';
import { take } from 'rxjs/operators';

@Component({
  selector: 'app-shop',
  templateUrl: './shop.component.html',
  styleUrls: ['./shop.component.scss']
})
export class ShopComponent implements OnInit {
  products: MerchItem[] = [];
  cart: CartItem[] = [];
  billingForm: FormGroup;

  constructor(
    private shopService: ShopService,
    private fb: FormBuilder
  ) {
    this.billingForm = this.fb.group({
      fullName: ['', Validators.required],
      address: ['', Validators.required],
      mobileMoney: [''],
      card: ['']
    });
  }

  ngOnInit(): void {
    this.products = this.shopService.getProducts();
    this.shopService.getCart().subscribe(cart => this.cart = cart);
  }

  addToCart(item: MerchItem) {
    this.shopService.addToCart(item);
  }

  updateQuantity(itemId: number, newQuantity: number) {
    this.shopService.updateQuantity(itemId, newQuantity);
  }

  totalPrice(): number {
    return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  }

  checkout() {
    if (this.billingForm.valid) {
      this.shopService.processPayment(this.billingForm.value);
      alert('Payment successful! Thank you for your purchase.');
      this.shopService.clearCart();
      this.billingForm.reset();
    }
  }
}