import 'package:flutter/material.dart';

class BurnRequestPage extends StatefulWidget {
  const BurnRequestPage({super.key});

  @override
  State<BurnRequestPage> createState() => _BurnRequestPageState();
}

class _BurnRequestPageState extends State<BurnRequestPage> {
  final _formKey = GlobalKey<FormState>();

  // ตัวแปรเก็บข้อมูลฟอร์ม
  String? village;
  DateTime? selectedDate;
  double area = 0;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFFF3E0), // สีพื้นหลังอ่อน
      appBar: AppBar(
        title: const Text('ขออนุญาตเผา'),
        backgroundColor: const Color(0xFFDD6B00), // สีส้มเข้ม
        foregroundColor: Colors.white,
      ),
      body: Container(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: ListView(
            children: [
              const Text(
                'กรอกข้อมูลการขอเผา',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFFEF6C00), // ส้มกลาง
                ),
              ),
              const SizedBox(height: 16),

              // หมู่บ้าน
              TextFormField(
                decoration: InputDecoration(
                  labelText: 'หมู่บ้าน',
                  labelStyle: const TextStyle(color: Color(0xFFEF6C00)),
                  border: OutlineInputBorder(
                    borderSide: const BorderSide(color: Color(0xFFEF6C00)),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  focusedBorder: const OutlineInputBorder(
                    borderSide: BorderSide(color: Color(0xFFDD6B00), width: 2),
                  ),
                ),
                validator: (value) =>
                    value == null || value.isEmpty ? 'กรุณากรอกหมู่บ้าน' : null,
                onSaved: (value) => village = value,
              ),
              const SizedBox(height: 16),

              // วันที่เผา
              ListTile(
                contentPadding: EdgeInsets.zero,
                title: Text(
                  selectedDate == null
                      ? 'เลือกวันที่ต้องการเผา'
                      : 'วันที่เผา: ${selectedDate!.toLocal()}'.split(' ')[0],
                  style: const TextStyle(color: Color(0xFF212121)),
                ),
                trailing: const Icon(Icons.calendar_month, color: Color(0xFFEF6C00)),
                onTap: () async {
                  final now = DateTime.now();
                  final picked = await showDatePicker(
                    context: context,
                    initialDate: now,
                    firstDate: now,
                    lastDate: now.add(const Duration(days: 7)),
                  );
                  if (picked != null) {
                    setState(() {
                      selectedDate = picked;
                    });
                  }
                },
              ),
              const SizedBox(height: 16),

              // พื้นที่ (ไร่)
              TextFormField(
                decoration: InputDecoration(
                  labelText: 'พื้นที่ (ไร่)',
                  labelStyle: const TextStyle(color: Color(0xFFEF6C00)),
                  border: OutlineInputBorder(
                    borderSide: const BorderSide(color: Color(0xFFEF6C00)),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  focusedBorder: const OutlineInputBorder(
                    borderSide: BorderSide(color: Color(0xFFDD6B00), width: 2),
                  ),
                ),
                keyboardType: TextInputType.number,
                validator: (value) {
                  if (value == null || value.isEmpty) return 'กรุณากรอกพื้นที่';
                  final numValue = double.tryParse(value);
                  if (numValue == null || numValue <= 0) return 'ต้องมากกว่า 0';
                  return null;
                },
                onSaved: (value) => area = double.parse(value ?? '0'),
              ),
              const SizedBox(height: 24),

              // ปุ่มส่งคำขอ
              SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton.icon(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFFEF6C00),
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  onPressed: () {
                    if (_formKey.currentState?.validate() ?? false) {
                      _formKey.currentState!.save();
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(
                          content: Text('ส่งคำขอเผาสำเร็จ'),
                          backgroundColor: Colors.green,
                        ),
                      );
                    }
                  },
                  icon: const Icon(Icons.fireplace),
                  label: const Text(
                    'ส่งคำขอ',
                    style: TextStyle(fontWeight: FontWeight.bold),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
