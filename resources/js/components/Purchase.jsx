import React, { Component } from "react";
import { createRoot } from "react-dom/client";
import axios from "axios";
import Swal from "sweetalert2";
import { sum } from "lodash";

class Purchase extends Component {
    constructor(props) {
        super(props);
        this.state = {
            cart: [],
            products: [],
            suppliers: [],
            search: "",
            supplier_id: "",
            purchase_date: new Date().toISOString().split('T')[0],
            status: "completed",
            notes: "",
            translations: {},
        };

        this.loadCart = this.loadCart.bind(this);
        this.loadProducts = this.loadProducts.bind(this);
        this.loadSuppliers = this.loadSuppliers.bind(this);
        this.handleChangeSearch = this.handleChangeSearch.bind(this);
        this.handleSearch = this.handleSearch.bind(this);
        this.addProductToCart = this.addProductToCart.bind(this);
        this.handleChangeQty = this.handleChangeQty.bind(this);
        this.handleChangePrice = this.handleChangePrice.bind(this);
        this.handleClickDelete = this.handleClickDelete.bind(this);
        this.handleEmptyCart = this.handleEmptyCart.bind(this);
        this.setSupplierId = this.setSupplierId.bind(this);
        this.handleDateChange = this.handleDateChange.bind(this);
        this.handleStatusChange = this.handleStatusChange.bind(this);
        this.handleNotesChange = this.handleNotesChange.bind(this);
        this.handleClickSubmit = this.handleClickSubmit.bind(this);
        this.loadTranslations = this.loadTranslations.bind(this);
    }

    componentDidMount() {
        this.loadTranslations();
        this.loadSuppliers();
        this.loadProducts();
        this.loadCart();
    }

    loadTranslations() {
        axios
            .get("/admin/locale/cart")
            .then((res) => {
                const translations = res.data;
                this.setState({ translations });
            })
            .catch((error) => {
                console.error("Error loading translations:", error);
                this.setState({ translations: {} });
            });
    }

    loadSuppliers() {
        axios.get(`/admin/suppliers`, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((res) => {
            console.log('Suppliers API Response:', res.data);
            console.log('Is Array?', Array.isArray(res.data));
            console.log('Has data property?', res.data.data);

            // Handle both array and paginated response
            const suppliers = Array.isArray(res.data) ? res.data : (res.data.data || []);
            console.log('Final suppliers:', suppliers);

            this.setState({ suppliers });
        }).catch((error) => {
            console.error("Error loading suppliers:", error);
            this.setState({ suppliers: [] });
        });
    }

    loadProducts(search = "") {
        const query = !!search ? `?search=${search}` : "";
        axios.get(`/admin/products${query}`, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((res) => {
            const products = res.data.data || [];
            this.setState({ products });
        }).catch((error) => {
            console.error("Error loading products:", error);
            this.setState({ products: [] });
        });
    }

    loadCart() {
        axios.get("/admin/purchase-cart", {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((res) => {
            const cart = Array.isArray(res.data) ? res.data : [];
            this.setState({ cart });
        }).catch((error) => {
            console.error("Error loading cart:", error);
            this.setState({ cart: [] });
        });
    }

    handleChangeSearch(event) {
        const search = event.target.value;
        this.setState({ search });
    }

    handleSearch(event) {
        if (event.keyCode === 13) {
            this.loadProducts(event.target.value);
        }
    }

    addProductToCart(product) {
        // Check if product already in cart
        let cartItem = this.state.cart.find((c) => c.id === product.id);

        if (cartItem) {
            // Update quantity
            this.setState({
                cart: this.state.cart.map((c) => {
                    if (c.id === product.id) {
                        c.pivot.quantity = c.pivot.quantity + 1;
                    }
                    return c;
                }),
            });
        } else {
            // Add new item with purchase price
            const newProduct = {
                ...product,
                pivot: {
                    quantity: 1,
                    purchase_price: product.purchase_price || 0,
                    product_id: product.id,
                    user_id: 1,
                },
            };
            this.setState({ cart: [...this.state.cart, newProduct] });
        }

        // Sync with backend
        axios
            .post("/admin/purchase-cart", { barcode: product.barcode })
            .then((res) => {
                console.log("Added to cart:", res.data);
            })
            .catch((err) => {
                Swal.fire("Error!", err.response?.data?.message || "Failed to add product", "error");
            });
    }

    handleChangeQty(product_id, qty) {
        const cart = this.state.cart.map((c) => {
            if (c.id === product_id) {
                c.pivot.quantity = qty;
            }
            return c;
        });

        this.setState({ cart });
        if (!qty) return;

        axios
            .post("/admin/purchase-cart/change-qty", { product_id, quantity: qty })
            .then((res) => {
                console.log("Quantity updated");
            })
            .catch((err) => {
                Swal.fire("Error!", err.response?.data?.message || "Failed to update quantity", "error");
            });
    }

    handleChangePrice(product_id, price) {
        const cart = this.state.cart.map((c) => {
            if (c.id === product_id) {
                c.pivot.purchase_price = price;
            }
            return c;
        });

        this.setState({ cart });
        if (!price) return;

        axios
            .post("/admin/purchase-cart/change-price", { product_id, purchase_price: price })
            .then((res) => {
                console.log("Price updated");
            })
            .catch((err) => {
                Swal.fire("Error!", err.response?.data?.message || "Failed to update price", "error");
            });
    }

    getTotal(cart) {
        const total = cart.map((c) => c.pivot.quantity * (c.pivot.purchase_price || 0));
        return sum(total).toFixed(2);
    }

    handleClickDelete(product_id) {
        axios
            .post("/admin/purchase-cart/delete", { product_id, _method: "DELETE" })
            .then((res) => {
                const cart = this.state.cart.filter((c) => c.id !== product_id);
                this.setState({ cart });
            })
            .catch((err) => {
                Swal.fire("Error!", err.response?.data?.message || "Failed to delete", "error");
            });
    }

    handleEmptyCart() {
        axios.post("/admin/purchase-cart/empty", { _method: "DELETE" }).then((res) => {
            this.setState({ cart: [] });
        }).catch((err) => {
            Swal.fire("Error!", err.response?.data?.message || "Failed to empty cart", "error");
        });
    }

    setSupplierId(event) {
        this.setState({ supplier_id: event.target.value });
    }

    handleDateChange(event) {
        this.setState({ purchase_date: event.target.value });
    }

    handleStatusChange(event) {
        this.setState({ status: event.target.value });
    }

    handleNotesChange(event) {
        this.setState({ notes: event.target.value });
    }

    handleClickSubmit() {
        const { supplier_id, purchase_date, status, notes, cart, suppliers } = this.state;

        // Validation
        if (!supplier_id) {
            Swal.fire("Error!", "Please select a supplier", "error");
            return;
        }

        if (cart.length === 0) {
            Swal.fire("Error!", "Please add at least one product", "error");
            return;
        }

        const total_amount = this.getTotal(cart);
        const items = cart.map(c => ({
            product_id: c.id,
            quantity: c.pivot.quantity,
            purchase_price: c.pivot.purchase_price || 0
        }));

        // Get supplier info safely
        const suppliersList = Array.isArray(suppliers) ? suppliers : [];
        const supplier = suppliersList.find(s => s.id == supplier_id);
        const supplierName = supplier ? `${supplier.first_name} ${supplier.last_name}` : 'Unknown';

        Swal.fire({
            title: "Confirm Purchase",
            html: `
                <div style="text-align: left;">
                    <p><strong>Supplier:</strong> ${supplierName}</p>
                    <p><strong>Date:</strong> ${purchase_date}</p>
                    <p><strong>Total Amount:</strong> ${window.APP.currency_symbol} ${total_amount}</p>
                    <p><strong>Status:</strong> ${status}</p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: "Save Purchase",
            cancelButtonText: "Cancel",
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return axios
                    .post("/admin/purchases", {
                        supplier_id,
                        purchase_date,
                        total_amount,
                        status,
                        notes,
                        items
                    })
                    .then((res) => {
                        this.loadCart();
                        return res.data;
                    })
                    .catch((err) => {
                        Swal.showValidationMessage(err.response?.data?.message || "Failed to create purchase");
                    });
            },
            allowOutsideClick: () => !Swal.isLoading(),
        }).then((result) => {
            if (result.value) {
                Swal.fire("Success!", "Purchase created successfully!", "success");
                // Clear form
                this.setState({
                    cart: [],
                    supplier_id: "",
                    purchase_date: new Date().toISOString().split('T')[0],
                    status: "completed",
                    notes: ""
                });
            }
        });
    }

    render() {
        const {
            cart = [],
            products = [],
            suppliers = [],
            search = "",
            supplier_id,
            purchase_date,
            status,
            notes,
            translations = {}
        } = this.state;

        // Ensure suppliers is always an array
        const suppliersList = Array.isArray(suppliers) ? suppliers : [];

        return (
            <div className="row purchase-container">
                {/* LEFT SIDE - Product Selector */}
                <div className="col-lg-8 col-md-7">
                    <div className="card">
                        <div className="card-body">
                            <div className="product-search mb-3">
                                <input
                                    type="text"
                                    className="form-control form-control-lg"
                                    placeholder={(translations["search_product"] || "Search Product") + "..."}
                                    value={search}
                                    onChange={this.handleChangeSearch}
                                    onKeyDown={this.handleSearch}
                                />
                            </div>
                            <div className="order-product">
                                {products.map((p) => (
                                    <div
                                        onClick={() => this.addProductToCart(p)}
                                        key={p.id}
                                        className="item"
                                    >
                                        <img src={p.image_url} alt={p.name} />
                                        <h5>{p.name}</h5>
                                        <small className="text-muted">Stock: {p.quantity}</small>
                                        {p.purchase_price && (
                                            <><br /><small className="text-success font-weight-bold">Cost: {window.APP.currency_symbol}{p.purchase_price}</small></>
                                        )}
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>

                {/* RIGHT SIDE - Purchase Cart */}
                <div className="col-lg-4 col-md-5">
                    <div className="cart-card">
                        {/* Supplier & Date Card */}
                        <div className="card card-primary card-outline">
                            <div className="card-header">
                                <h3 className="card-title">
                                    <i className="fas fa-truck mr-2"></i>Purchase Information
                                </h3>
                            </div>
                            <div className="card-body">
                                <div className="form-group">
                                    <label>Supplier <span className="text-danger">*</span></label>
                                    <select
                                        className="form-control"
                                        value={supplier_id}
                                        onChange={this.setSupplierId}
                                    >
                                        <option value="">Select Supplier</option>
                                        {suppliersList.map((sup) => (
                                            <option
                                                key={sup.id}
                                                value={sup.id}
                                            >{`${sup.first_name} ${sup.last_name}`}</option>
                                        ))}
                                    </select>
                                </div>
                                <div className="form-group">
                                    <label>Purchase Date <span className="text-danger">*</span></label>
                                    <input
                                        type="date"
                                        className="form-control"
                                        value={purchase_date}
                                        onChange={this.handleDateChange}
                                    />
                                </div>
                            </div>
                        </div>

                        {/* Cart Items Card */}
                        <div className="card card-secondary card-outline">
                            <div className="card-header">
                                <h3 className="card-title">
                                    <i className="fas fa-shopping-basket mr-2"></i>Items
                                </h3>
                            </div>
                            <div className="card-body p-0 purchase-cart">
                                <table className="table table-sm table-hover mb-0">
                                    <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th width="70">Qty</th>
                                        <th width="90">Price</th>
                                        <th width="40"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {cart.map((c) => (
                                        <tr key={c.id}>
                                            <td>
                                                <small className="font-weight-bold">{c.name}</small>
                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    className="form-control form-control-sm"
                                                    value={c.pivot.quantity}
                                                    onChange={(event) =>
                                                        this.handleChangeQty(
                                                            c.id,
                                                            event.target.value
                                                        )
                                                    }
                                                    min="1"
                                                />
                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    className="form-control form-control-sm"
                                                    value={c.pivot.purchase_price || 0}
                                                    onChange={(event) =>
                                                        this.handleChangePrice(
                                                            c.id,
                                                            event.target.value
                                                        )
                                                    }
                                                    min="0"
                                                    step="0.01"
                                                />
                                            </td>
                                            <td>
                                                <button
                                                    className="btn btn-danger btn-xs"
                                                    onClick={() => this.handleClickDelete(c.id)}
                                                    title="Remove"
                                                >
                                                    <i className="fas fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                    </tbody>
                                </table>
                                {cart.length === 0 && (
                                    <div className="text-center text-muted py-4">
                                        <i className="fas fa-inbox fa-3x mb-2"></i>
                                        <p>No items added yet</p>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Notes */}
                        {cart.length > 0 && (
                            <div className="card">
                                <div className="card-body">
                                    <div className="form-group mb-0">
                                        <label className="small">Notes (optional)</label>
                                        <textarea
                                            className="form-control form-control-sm"
                                            placeholder="Add notes..."
                                            rows="2"
                                            value={notes}
                                            onChange={this.handleNotesChange}
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Status */}
                        {cart.length > 0 && (
                            <div className="card">
                                <div className="card-body">
                                    <label className="small font-weight-bold mb-2">
                                        <i className="fas fa-flag mr-1"></i>Status
                                    </label>
                                    <div className="status-selector">
                                        <div className="form-check">
                                            <input
                                                className="form-check-input"
                                                type="radio"
                                                name="status"
                                                id="status-pending"
                                                value="pending"
                                                checked={status === "pending"}
                                                onChange={this.handleStatusChange}
                                            />
                                            <label className="form-check-label" htmlFor="status-pending">
                                                <i className="fas fa-clock text-warning mr-1"></i>Pending
                                            </label>
                                        </div>
                                        <div className="form-check">
                                            <input
                                                className="form-check-input"
                                                type="radio"
                                                name="status"
                                                id="status-completed"
                                                value="completed"
                                                checked={status === "completed"}
                                                onChange={this.handleStatusChange}
                                            />
                                            <label className="form-check-label" htmlFor="status-completed">
                                                <i className="fas fa-check-circle text-success mr-1"></i>Completed
                                            </label>
                                        </div>
                                        <div className="form-check">
                                            <input
                                                className="form-check-input"
                                                type="radio"
                                                name="status"
                                                id="status-cancelled"
                                                value="cancelled"
                                                checked={status === "cancelled"}
                                                onChange={this.handleStatusChange}
                                            />
                                            <label className="form-check-label" htmlFor="status-cancelled">
                                                <i className="fas fa-times-circle text-danger mr-1"></i>Cancelled
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Total & Actions */}
                        {cart.length > 0 && (
                            <>
                                <div className="purchase-total text-center">
                                    <div className="small mb-1">Total Amount</div>
                                    <div className="amount">{window.APP.currency_symbol} {this.getTotal(cart)}</div>
                                </div>

                                <div className="purchase-actions">
                                    <div className="row">
                                        <div className="col-6">
                                            <button
                                                type="button"
                                                className="btn btn-danger btn-block"
                                                onClick={this.handleEmptyCart}
                                            >
                                                <i className="fas fa-times mr-1"></i>Cancel
                                            </button>
                                        </div>
                                        <div className="col-6">
                                            <button
                                                type="button"
                                                className="btn btn-primary btn-block"
                                                disabled={!supplier_id}
                                                onClick={this.handleClickSubmit}
                                            >
                                                <i className="fas fa-save mr-1"></i>Save
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </>
                        )}
                    </div>
                </div>
            </div>
        );
    }
}

export default Purchase;

// Render component
const root = document.getElementById("purchase");
if (root) {
    const rootInstance = createRoot(root);
    rootInstance.render(<Purchase />);
}
